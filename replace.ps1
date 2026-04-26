 = Get-ChildItem -Path "resources\views" -Recurse -Include *.blade.php

foreach ($file in $files) {
    $content = Get-Content $file.FullName -Raw

    # Replace bg-[#008080] in buttons/badges with gradient and effects
    $content = $content -replace 'bg-\[#008080\](?![^<]*\btext-transparent\b)', 'bg-gradient-to-br from-[#4FC3F7] to-[#20B2AA] shadow-[inset_0_1px_1px_rgba(255,255,255,0.4)] shadow-[#20B2AA]/30'
    
    # Clean up duplicate shadows if they exist
    $content = $content -replace 'shadow-md shadow-\[#20B2AA\]/30', 'shadow-md'
    $content = $content -replace 'shadow-lg shadow-\[#008080\]/20', 'shadow-lg'
    $content = $content -replace 'shadow-md shadow-\[#008080\]/20', 'shadow-md'
    $content = $content -replace 'shadow-\[#008080\]/20', 'shadow-[#20B2AA]/30'

    # Replace hover:bg-[#006666] or hover:bg-[#008080]
    $content = $content -replace 'hover:bg-\[#006666\]', 'hover:from-[#3ab0e6] hover:to-[#1a9992]'
    
    # Replace text-[#008080] for icons/text
    # Wait, the prompt says "buttons and icons currently colored in #008080".
    # For text, we should just use text-transparent bg-clip-text bg-gradient-to-br from-[#4FC3F7] to-[#20B2AA]
    $content = $content -replace 'text-\[#008080\]', 'text-transparent bg-clip-text bg-gradient-to-br from-[#4FC3F7] to-[#20B2AA]'
    
    # For selection:bg-[#008080], we might just replace it with the turquoise color since gradient doesn't work well on selection
    $content = $content -replace 'selection:bg-gradient-to-br from-\[#4FC3F7\] to-\[#20B2AA\] shadow-\[inset_0_1px_1px_rgba\(255,255,255,0\.4\)\] shadow-\[#20B2AA\]/30', 'selection:bg-[#20B2AA]'

    Set-Content -Path $file.FullName -Value $content
}
