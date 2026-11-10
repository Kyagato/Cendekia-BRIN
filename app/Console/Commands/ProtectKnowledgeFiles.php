<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class ProtectKnowledgeFiles extends Command
{
    protected $signature = 'knowledge:protect-files';

    protected $description = 'Move legacy knowledge uploads out of public storage without changing their URLs';

    public function handle(): int
    {
        $public = Storage::disk('public');
        $private = Storage::disk('knowledge');
        $count = 0;

        foreach (['uploads', 'documents', 'knowledge_files', 'knowledge_covers'] as $directory) {
            foreach ($public->allFiles($directory) as $path) {
                // Copy and verify before removing the publicly accessible original.
                if (! $private->exists($path)) {
                    $stream = $public->readStream($path);
                    try {
                        if (! $private->put($path, $stream)) {
                            throw new RuntimeException('Unable to protect file: '.$path);
                        }
                    } finally {
                        if (is_resource($stream)) {
                            fclose($stream);
                        }
                    }
                }

                if (hash_file('sha256', $public->path($path)) !== hash_file('sha256', $private->path($path))) {
                    throw new RuntimeException('File verification failed; public original retained: '.$path);
                }
                if (! $public->delete($path)) {
                    throw new RuntimeException('Unable to remove public original: '.$path);
                }
                $count++;
            }
        }

        $this->info("Protected {$count} knowledge files.");

        return self::SUCCESS;
    }
}
