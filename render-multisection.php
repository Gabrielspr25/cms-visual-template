<?php
// Función para renderizar secciones multiformato
function renderMultiformatSection($section) {
    if (!isset($section['blocks']) || !is_array($section['blocks'])) {
        return '';
    }
    
    $html = '';
    $bgStyle = '';
    
    // Aplicar fondo si está definido
    if (!empty($section['bg'])) {
        $bgStyle = "background: {$section['bg']};";
    }
    
    // Contenedor de la sección
    $html .= '<section class="multiformat-section" style="' . $bgStyle . '">';
    $html .= '<div class="container">';
    
    // Título de la sección si existe
    if (!empty($section['titulo'])) {
        $html .= '<h2 class="section-title">' . htmlspecialchars($section['titulo']) . '</h2>';
    }
    
    // Renderizar cada bloque
    foreach ($section['blocks'] as $block) {
        $html .= renderBlock($block);
    }
    
    $html .= '</div>';
    $html .= '</section>';
    
    return $html;
}

function renderBlock($block) {
    if (!isset($block['type'])) {
        return '';
    }
    
    $content = $block['content'] ?? '';
    $properties = $block['properties'] ?? [];
    
    switch ($block['type']) {
        case 'text':
            return renderTextBlock($content, $properties);
            
        case 'heading':
            return renderHeadingBlock($content, $properties);
            
        case 'list':
            return renderListBlock($content, $properties);
            
        case 'image':
            return renderImageBlock($content, $properties);
            
        case 'video':
            return renderVideoBlock($content, $properties);
            
        case 'columns':
            return renderColumnsBlock($content, $properties);
            
        case 'button':
            return renderButtonBlock($content, $properties);
            
        case 'separator':
            return renderSeparatorBlock($properties);
            
        case 'spacer':
            return renderSpacerBlock($content, $properties);
            
        case 'html':
            return renderHtmlBlock($content);
            
        default:
            return '<!-- Bloque desconocido: ' . htmlspecialchars($block['type']) . ' -->';
    }
}

function renderTextBlock($content, $properties) {
    $fontSize = $properties['fontSize'] ?? 16;
    $color = $properties['color'] ?? '#333333';
    $alignment = $properties['alignment'] ?? 'left';
    
    $style = "font-size: {$fontSize}px; color: {$color}; text-align: {$alignment}; margin-bottom: 16px;";
    
    return '<p style="' . $style . '">' . nl2br(htmlspecialchars($content)) . '</p>';
}

function renderHeadingBlock($content, $properties) {
    $level = $properties['level'] ?? 'h2';
    $color = $properties['color'] ?? '#333333';
    $alignment = $properties['alignment'] ?? 'left';
    
    $style = "color: {$color}; text-align: {$alignment}; margin-bottom: 16px;";
    
    return '<' . $level . ' style="' . $style . '">' . htmlspecialchars($content) . '</' . $level . '>';
}

function renderListBlock($content, $properties) {
    if (!is_array($content) || empty($content)) {
        return '';
    }
    
    $style = $properties['style'] ?? 'ul';
    $color = $properties['color'] ?? '#333333';
    
    $listStyle = "color: {$color}; margin-bottom: 16px;";
    
    $html = '<' . $style . ' style="' . $listStyle . '">';
    
    foreach ($content as $item) {
        $html .= '<li>' . htmlspecialchars($item) . '</li>';
    }
    
    $html .= '</' . $style . '>';
    
    return $html;
}

function renderImageBlock($content, $properties) {
    if (!is_array($content) || empty($content['images'])) {
        return '';
    }
    
    $images = $content['images'];
    $caption = $content['caption'] ?? '';
    $columns = $properties['columns'] ?? 3;
    $spacing = $properties['spacing'] ?? 10;
    $alignment = $properties['alignment'] ?? 'center';
    $showCaptions = $properties['showCaptions'] ?? true;
    
    $containerStyle = "text-align: {$alignment}; margin-bottom: 16px;";
    $gridStyle = "display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: {$spacing}px; margin-bottom: 16px;";
    
    $html = '<div style="' . $containerStyle . '">';
    $html .= '<div style="' . $gridStyle . '">';
    
    foreach ($images as $image) {
        $src = $image['src'] ?? '';
        $alt = $image['alt'] ?? '';
        $imgCaption = $image['caption'] ?? '';
        
        if (!empty($src)) {
            $html .= '<div style="border-radius: 8px; overflow: hidden; background: #f5f5f5;">';
            $html .= '<img src="' . htmlspecialchars($src) . '" alt="' . htmlspecialchars($alt) . '" style="width: 100%; height: 250px; object-fit: cover;">';
            
            if ($showCaptions && !empty($imgCaption)) {
                $html .= '<div style="padding: 12px; font-size: 14px; color: #666; background: white;">' . htmlspecialchars($imgCaption) . '</div>';
            }
            
            $html .= '</div>';
        }
    }
    
    $html .= '</div>';
    
    // Leyenda general de la galería
    if (!empty($caption)) {
        $html .= '<div style="text-align: center; font-size: 16px; color: #666; margin-top: 16px; font-style: italic;">' . htmlspecialchars($caption) . '</div>';
    }
    
    $html .= '</div>';
    
    return $html;
}

function renderVideoBlock($content, $properties) {
    if (!is_array($content) || empty($content['src'])) {
        return '';
    }
    
    $src = $content['src'];
    $caption = $content['caption'] ?? '';
    $width = $properties['width'] ?? '100%';
    $alignment = $properties['alignment'] ?? 'center';
    
    $containerStyle = "text-align: {$alignment}; margin-bottom: 16px;";
    $videoStyle = "max-width: {$width}; height: auto; border-radius: 8px;";
    
    $html = '<div style="' . $containerStyle . '">';
    $html .= '<video controls style="' . $videoStyle . '">';
    $html .= '<source src="' . htmlspecialchars($src) . '" type="video/mp4">';
    $html .= 'Tu navegador no soporta video HTML5.';
    $html .= '</video>';
    
    if (!empty($caption)) {
        $html .= '<div style="font-size: 14px; color: #666; margin-top: 8px;">' . htmlspecialchars($caption) . '</div>';
    }
    
    $html .= '</div>';
    
    return $html;
}

function renderColumnsBlock($content, $properties) {
    if (!is_array($content) || !isset($content['content']) || !is_array($content['content'])) {
        return '';
    }
    
    $columns = $content['content'];
    $gap = $properties['gap'] ?? 20;
    
    $containerStyle = "display: flex; gap: {$gap}px; margin-bottom: 16px; flex-wrap: wrap;";
    
    $html = '<div style="' . $containerStyle . '">';
    
    foreach ($columns as $columnContent) {
        $columnStyle = "flex: 1; min-width: 200px;";
        $html .= '<div style="' . $columnStyle . '">' . nl2br(htmlspecialchars($columnContent)) . '</div>';
    }
    
    $html .= '</div>';
    
    return $html;
}

function renderButtonBlock($content, $properties) {
    if (!is_array($content)) {
        return '';
    }
    
    $text = $content['text'] ?? 'Botón';
    $url = $content['url'] ?? '#';
    $color = $properties['color'] ?? '#ffffff';
    $backgroundColor = $properties['backgroundColor'] ?? '#3182ce';
    $alignment = $properties['alignment'] ?? 'left';
    
    $containerStyle = "text-align: {$alignment}; margin-bottom: 16px;";
    $buttonStyle = "display: inline-block; padding: 12px 24px; background: {$backgroundColor}; color: {$color}; text-decoration: none; border-radius: 8px; font-weight: 500; transition: all 0.2s;";
    
    $html = '<div style="' . $containerStyle . '">';
    $html .= '<a href="' . htmlspecialchars($url) . '" style="' . $buttonStyle . '">' . htmlspecialchars($text) . '</a>';
    $html .= '</div>';
    
    return $html;
}

function renderSeparatorBlock($properties) {
    $color = $properties['color'] ?? '#cccccc';
    $thickness = $properties['thickness'] ?? 1;
    
    $style = "border: none; height: {$thickness}px; background: {$color}; margin: 20px 0;";
    
    return '<hr style="' . $style . '">';
}

function renderSpacerBlock($content, $properties) {
    $height = is_array($content) ? ($content['height'] ?? 50) : 50;
    $backgroundColor = $properties['backgroundColor'] ?? 'transparent';
    
    $style = "height: {$height}px; background: {$backgroundColor};";
    
    return '<div style="' . $style . '"></div>';
}

function renderHtmlBlock($content) {
    // Por seguridad, solo permitir ciertos tags HTML
    $allowedTags = '<p><br><strong><em><u><h1><h2><h3><h4><h5><h6><ul><ol><li><a><img><div><span>';
    return strip_tags($content, $allowedTags);
}
?>