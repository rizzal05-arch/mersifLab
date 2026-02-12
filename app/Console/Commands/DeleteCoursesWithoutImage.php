<?php

namespace App\Console\Commands;

use App\Models\ClassModel;
use Illuminate\Console\Command;

class DeleteCoursesWithoutImage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'courses:delete-without-image';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all courses without image/cover';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Find courses without image
        $courses = ClassModel::whereNull('image')
            ->orWhere('image', '')
            ->get();

        if ($courses->isEmpty()) {
            $this->info("✅ Semua course sudah memiliki image!");
            return 0;
        }

        $this->warn("Ditemukan " . $courses->count() . " course tanpa image:");
        
        foreach ($courses as $course) {
            $this->info("  - {$course->name} (ID: {$course->id})");
        }

        if ($this->confirm('Hapus semua course tanpa image?')) {
            $deleted = ClassModel::whereNull('image')
                ->orWhere('image', '')
                ->delete();

            $this->info("✅ Berhasil menghapus {$deleted} course!");
            return 0;
        }

        $this->info("❌ Dibatalkan");
        return 0;
    }
}
