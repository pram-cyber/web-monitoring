<?php

namespace Tests\Feature;

use App\Models\TrashBin;
use App\Models\SensorLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SensorLogCalculationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_calculates_correct_values_for_normal_distance()
    {
        // 1. Create a trash bin with depth 55 cm
        $bin = TrashBin::create([
            'name' => 'Tong Sampah Test',
            'location' => 'Jember',
            'latitude' => -8.166,
            'longitude' => 113.704,
            'max_depth_cm' => 55,
            'percentage' => 0,
            'distance_cm' => 59.4,
            'status' => 'empty',
            'is_active' => true,
        ]);

        // 2. Post sensor log with distance_cm = 25
        // Expected calculations:
        // clampedDistance = max(11.0, min(59.4, 25)) = 25
        // sisaRuangVal = ((25 - 11.0) / (59.4 - 11.0)) * 55 = (14.0 / 48.4) * 55 = 15.909 cm
        // sisaRuang = round(15.909) = 16
        // tinggiSampahVal = 55 - 15.909 = 39.091 cm
        // tinggiSampah = round(39.091) = 39
        // percentage = (39.091 / 55) * 100 = 71.07% -> round() -> 71% (which is Normal: between 25 and 85)
        // status = 'half'
        $response = $this->postJson('/api/sensor-logs', [
            'trash_bin_id' => $bin->id,
            'distance_cm' => 25,
            'latitude' => -8.1661,
            'longitude' => 113.7041,
        ]);

        $response->assertStatus(201);

        // Verify the database update on the TrashBin model
        $bin->refresh();
        $this->assertEquals(71, $bin->percentage);
        $this->assertEquals(39, $bin->tinggi_sampah);
        $this->assertEquals(16, $bin->sisa_ruang);
        $this->assertEquals(16, $bin->distance_cm); // distance_cm should match sisa_ruang
        $this->assertEquals('half', $bin->status);
    }

    /** @test */
    public function it_calculates_correct_values_for_full_distance()
    {
        $bin = TrashBin::create([
            'name' => 'Tong Sampah Test 2',
            'location' => 'Jember',
            'latitude' => -8.166,
            'longitude' => 113.704,
            'max_depth_cm' => 55,
            'percentage' => 0,
            'distance_cm' => 59.4,
            'status' => 'empty',
            'is_active' => true,
        ]);

        // Post sensor log with distance_cm = 12
        // Expected calculations:
        // clampedDistance = max(11.0, min(59.4, 12)) = 12
        // sisaRuangVal = ((12 - 11.0) / (59.4 - 11.0)) * 55 = (1.0 / 48.4) * 55 = 1.136 cm
        // sisaRuang = round(1.136) = 1
        // tinggiSampahVal = 55 - 1.136 = 53.864 cm
        // tinggiSampah = round(53.864) = 54
        // percentage = (53.864 / 55) * 100 = 97.9% -> round() -> 98% (which is >85%, Full)
        // status = 'full'
        $response = $this->postJson('/api/sensor-logs', [
            'trash_bin_id' => $bin->id,
            'distance_cm' => 12,
            'latitude' => -8.1661,
            'longitude' => 113.7041,
        ]);

        $response->assertStatus(201);

        $bin->refresh();
        $this->assertEquals(98, $bin->percentage);
        $this->assertEquals(54, $bin->tinggi_sampah);
        $this->assertEquals(1, $bin->sisa_ruang);
        $this->assertEquals(1, $bin->distance_cm); // distance_cm should match sisa_ruang
        $this->assertEquals('full', $bin->status);

        // Verify history log was created
        $historyLog = SensorLog::where('trash_bin_id', $bin->id)
            ->where('is_history', true)
            ->first();

        $this->assertNotNull($historyLog);
        $this->assertEquals('full', $historyLog->status);
    }

    /** @test */
    public function it_logs_normal_status_transition_in_history()
    {
        $bin = TrashBin::create([
            'name' => 'Tong Sampah Test 3',
            'location' => 'Jember',
            'latitude' => -8.166,
            'longitude' => 113.704,
            'max_depth_cm' => 55,
            'percentage' => 0,
            'distance_cm' => 59.4,
            'status' => 'empty',
            'is_active' => true,
        ]);

        // 1. Post sensor log with distance_cm = 35 (around 50% percentage -> half/Normal)
        // Expected percentage = round(( (55 - ((35 - 11.0)/(59.4 - 11.0))*55) / 55 ) * 100)
        // = round(( (55 - (24.0/48.4)*55) / 55 ) * 100) = round((1 - 0.4958) * 100) = 50%
        $response = $this->postJson('/api/sensor-logs', [
            'trash_bin_id' => $bin->id,
            'distance_cm' => 35,
            'latitude' => -8.1661,
            'longitude' => 113.7041,
        ]);
        $response->assertStatus(201);

        $bin->refresh();
        $this->assertEquals(50, $bin->percentage);
        $this->assertEquals('half', $bin->status);

        // Verify Normal ('half') history log was created
        $halfHistoryLog = SensorLog::where('trash_bin_id', $bin->id)
            ->where('is_history', true)
            ->where('status', 'half')
            ->first();
        $this->assertNotNull($halfHistoryLog);

        // 2. Post another log at 33 cm (around 55% -> Normal) - should NOT create another history log
        $response = $this->postJson('/api/sensor-logs', [
            'trash_bin_id' => $bin->id,
            'distance_cm' => 33,
            'latitude' => -8.1661,
            'longitude' => 113.7041,
        ]);
        $response->assertStatus(201);

        $this->assertEquals(1, SensorLog::where('trash_bin_id', $bin->id)
            ->where('is_history', true)
            ->where('status', 'half')
            ->count());

        // 3. Post sensor log with distance_cm = 12 (around 98% -> Full) - should create full history log
        $response = $this->postJson('/api/sensor-logs', [
            'trash_bin_id' => $bin->id,
            'distance_cm' => 12,
            'latitude' => -8.1661,
            'longitude' => 113.7041,
        ]);
        $response->assertStatus(201);

        $fullHistoryLog = SensorLog::where('trash_bin_id', $bin->id)
            ->where('is_history', true)
            ->where('status', 'full')
            ->first();
        $this->assertNotNull($fullHistoryLog);

        // 4. Post sensor log with distance_cm = 59.4 (0% -> Empty) - should create empty history log
        $response = $this->postJson('/api/sensor-logs', [
            'trash_bin_id' => $bin->id,
            'distance_cm' => 59.4,
            'latitude' => -8.1661,
            'longitude' => 113.7041,
        ]);
        $response->assertStatus(201);

        $emptyHistoryLog = SensorLog::where('trash_bin_id', $bin->id)
            ->where('is_history', true)
            ->where('status', 'empty')
            ->first();
        $this->assertNotNull($emptyHistoryLog);
    }
}
