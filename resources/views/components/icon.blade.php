@props(['name', 'class' => ''])
@php
    $svgPath = public_path("icons/hugeicons/{$name}.svg");
    if (file_exists($svgPath)) {
        $svg = file_get_contents($svgPath);
        $svg = preg_replace('/\s*width="[^"]*"/', '', $svg);
        $svg = preg_replace('/\s*height="[^"]*"/', '', $svg);
        $svg = preg_replace('/stroke="#[0-9A-Fa-f]{3,8}"/', 'stroke="currentColor"', $svg);
        $svg = preg_replace('/fill="#[0-9A-Fa-f]{3,8}"/', 'fill="none"', $svg);
        $cls = 'inline-block' . ($class ? ' ' . $class : '');
        $svg = preg_replace('/<svg/', "<svg width=\"1em\" height=\"1em\" class=\"{$cls}\"", $svg, 1);
    } else {
        $svg = "<!-- icon not found: {$name} -->";
    }
@endphp
{!! $svg !!}
