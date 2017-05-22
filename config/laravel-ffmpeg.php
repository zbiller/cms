<?php

return [
    'default_disk' => 'local',

    'ffmpeg.binaries' => env('VIDEO_FFMPEG', 'ffmpeg'),

    'ffmpeg.threads'  => 12,

    'ffprobe.binaries' => env('VIDEO_FFPROBE', 'ffprobe'),

    'timeout' => 3600,
];
