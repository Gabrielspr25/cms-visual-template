<?php
// INDEX LIMPIO • MomVision • SS-Group

// --- Cargar data.json ---
$raw = @file_get_contents(__DIR__ . '/data.json');
$data = json_decode($raw ?: '[]', true);
if (!is_array($data)) $data = [];

// Helpers
function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function val($a,$k,$d=null){ return isset($a[$k]) ? $a[$k] : $d; }

// Data bases
$brand      = val($data, 'brand', []);
$secciones  = val($data, 'secciones', []);
$video      = val($data, 'video_banner', 'uploads/banner.mp4');
$fonts      = val($data, 'fonts', []);
$contacto   = val($data, 'contacto', []);
$formConf   = val($data, 'form', []);
$footer     = val($data, 'footer', []);
$socials    = val($data, 'socials', val($data, 'redes', [])); // usa socials o redes

// Google Fonts (según JSON)
$gf = [];
foreach (['body','headings','signature'] as $slot){
  $f = val($fonts, $slot, []);
  if (val($f,'type')==='google' && ($fam = val($f,'family'))){
    $weights = val($f,'weights','400');
    $gf[] = "family=".rawurlencode($fam).":wght@".rawurlencode($weights);
  }
}
$gf = array_unique($gf);
$gfHref = $gf ? "https://fonts.googleapis.com/css2?".implode('&',$gf)."&display=swap" : "";
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title><?= h(val($brand,'name','MomVision')) ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php if ($gfHref): ?>
<link href="<?= h($gfHref) ?>" rel="stylesheet">
<?php endif; ?>
<style>
  :root{
    --nav-bg:#ffffff;
    --nav-text:#0e1525;
    --accent:#0e7ac7;
    --card:#f6f8fb;
    --muted:#5b6573;
    --border:#dfe5ef;
  }
  *{box-sizing:border-box}
  html,body{margin:0;padding:0}
  body{
    font-family:<?= h(val($fonts['body']??[],'family','Inter')) ?>, system-ui, -apple-system, Segoe UI, Arial;
    color:#0e1525;
    background:#ffffff;
  }
  h1,h2,h3{
    font-family:<?= h(val($fonts['headings']??[],'family','Poppins')) ?>, system-ui, -apple-system, Segoe UI, Arial;
    margin:0 0 .5rem 0;
  }
  a{color:inherit;text-decoration:none}
  /* HEADER */
  header{position:sticky;top:0;z-index:1000;background:var(--nav-bg);border-bottom:1px solid var(--border);}
  .nav{max-width:1200px;margin:0 auto;display:flex;align-items:center;gap:12px;padding:10px 16px}
  .brand{display:flex;align-items:center;gap:10px}
  .brand img{height:42px;display:block}
  .brand strong{font-size:18px;color:var(--nav-text)}
  .spacer{flex:1}
  .menu{display:flex;flex-wrap:wrap;gap:6px}
  .menu a{padding:8px 10px;color:var(--nav-text);border-radius:8px}
  .menu a:hover{background:#eef5ff}
  /* HERO VIDEO */
  .hero{width:100%;height:70vh;min-height:360px;overflow:hidden;background:#000}
  .hero video{width:100%;height:100%;object-fit:cover;display:block}
  /* LAYOUT */
  .wrap{max-width:1200px;margin:28px auto;padding:0 16px}
  .section{background:#fff;border:1px solid var(--border);border-radius:16px;padding:18px;margin:18px 0}
  .section .lead{line-height:1.7}
  .thumb{width:100%;border-radius:12px;border:1px solid var(--border);display:block}
  .muted{color:var(--muted)}
  /* COLECCIÓN (cards iguales) */
  .grid{display:grid;gap:16px}
  @media(min-width:700px){ .grid.cols-4{grid-template-columns:repeat(2,1fr)} }
  @media(min-width:1000px){ .grid.cols-4{grid-template-columns:repeat(4,1fr)} }
  .card{background:var(--card);border:1px solid var(--border);border-radius:16px;display:flex;flex-direction:column;overflow:hidden}
  .media{width:100%;height:220px;background:#fff;display:block;overflow:hidden}
  .media img{width:100%;height:100%;object-fit:cover;display:block;transition:transform .35s ease}
  .media:hover img{transform:scale(1.03)}
  .card-body{display:flex;flex-direction:column;padding:14px;gap:8px;min-height:200px}
  .btn{background:var(--accent);color:#fff;border:none;padding:10px 14px;border-radius:10px;cursor:pointer;font-weight:600;text-align:center}
  .btn:hover{opacity:.92}
  .btn-secondary{background:#eef5ff;color:#0e1525}
  /* MODAL */
  .modal{position:fixed;inset:0;background:rgba(0,0,0,.6);display:none;align-items:center;justify-content:center;padding:18px;z-index:2000}
  .modal.show{display:flex}
  .m-box{background:#fff;max-width:920px;width:100%;border-radius:14px;position:relative;padding:16px;box-shadow:0 20px 60px rgba(0,0,0,.25)}
  .m-close{position:absolute;right:12px;top:6px;font-size:26px;line-height:1;cursor:pointer}
  .m-media{width:100%;max-height:62vh;overflow:hidden;border-radius:10px;border:1px solid var(--border);margin-bottom:12px}
  .m-media img{width:100%;height:100%;object-fit:contain;display:block;cursor:zoom-in}
  /* CONTACTO */
  .contact{border:1px solid var(--border);border-radius:16px;padding:18px;margin:18px 0}
  .c-form{max-width:640px;margin:14px auto 0 auto;display:flex;flex-direction:column;gap:10px}
  .c-form input,.c-form textarea{
    width:100%;padding:12px;border:1px solid var(--border);border-radius:10px;outline:none
  }
  .c-form textarea{min-height:120px;resize:vertical}
  .alert{padding:10px 12px;border-radius:10px;margin:10px 0;font-weight:600}
  .ok{background:#e7f6ee;color:#0d6b3a;border:1px solid #c6ebd6}
  .err{background:#fdecec;color:#9e1c1c;border:1px solid #f6cccc}
  /* FOOTER */
  footer{background:#0b1a2b;color:#dbe5ff;margin-top:26px}
  .footer-inner{max-width:1200px;margin:0 auto;padding:22px 16px;display:flex;flex-wrap:wrap;gap:14px;align-items:center}
  .footer-inner small{opacity:.9}
  /* WP FLOAT */
  .wp-float{
    position:fixed;right:16px;bottom:16px;background:#25D366;color:#fff;border-radius:999px;
    padding:12px 14px;font-weight:700;box-shadow:0 10px 30px rgba(37,211,102,.4);z-index:1300
  }
</style>
</head>
<body>

<header>
  <nav class="nav">
    <a href="#home" class="brand">
      <?php if($logo = val($brand,'logo')): ?>
        <img src="/<?= h($logo) ?>" alt="<?= h(val($brand,'name','MomVision')) ?>">
      <?php else: ?>
        <strong><?= h(val($brand,'name','MomVision')) ?></strong>
      <?php endif; ?>
    </a>
    <div class="spacer"></div>
    <div class="menu">
      <?php foreach ($secciones as $s):
        // Soportar tanto show_in_menu como mostrar_menu
        $show = isset($s['show_in_menu']) ? (bool)$s['show_in_menu'] : 
                (isset($s['mostrar_menu']) ? (bool)$s['mostrar_menu'] : true);
        if (!$show) continue; ?>
        <a href="#<?= h(val($s,'id','')) ?>"><?= h(val($s,'titulo','Sección')) ?></a>
      <?php endforeach; ?>
      <a href="#contacto">Contacto</a>
    </div>
  </nav>
</header>

<section id="home" class="hero">
  <video autoplay muted loop playsinline>
    <source src="/<?= h($video) ?>?t=<?= time() ?>" type="video/mp4">
  </video>
</section>

<div class="wrap">
  <?php foreach ($secciones as $s):
    $id = val($s,'id','sec');
    $tit = val($s,'titulo','Sección');
    $tipo = val($s,'tipo','texto');
    $bg = val($s,'bg','#ffffff');
    $border = (int)val($s,'border',1);
  ?>
    <?php if ($tipo==='texto'): ?>
      <section id="<?= h($id) ?>" class="section" style="background:<?= h($bg) ?>;border-width:<?= $border ?>px">
        <h2><?= h($tit) ?></h2>
        <?php if($img = val($s,'imagen')): ?>
          <img class="thumb" src="/<?= h($img) ?>" alt="">
          <div style="height:10px"></div>
        <?php endif; ?>
        <div class="lead"><?= val($s['contenido']??[],'html','') ?></div>
      </section>

    <?php elseif ($tipo==='coleccion'): ?>
      <section id="<?= h($id) ?>" class="section" style="background:<?= h($bg) ?>;border-width:<?= $border ?>px">
        <h2><?= h($tit) ?></h2>
        <div class="grid cols-4">
          <?php foreach(val($s,'columns',[]) as $i=>$c): $mid = $id.'__m'.$i; ?>
            <article class="card">
              <?php if($ci = val($c,'imagen')): ?>
                <a class="media" href="javascript:void(0)" data-open="<?= h($mid) ?>">
                  <img src="/<?= h($ci) ?>" alt="">
                </a>
              <?php else: ?>
                <div class="media" style="background: var(--card); display: flex; align-items: center; justify-content: center; color: var(--muted);">
                  <i class="fas fa-image" style="font-size: 24px;"></i>
                </div>
              <?php endif; ?>
              <div class="card-body">
                <h3 style="margin:0"><?= h(val($c,'titulo','')) ?></h3>
                <div class="muted"><?= h(val($c,'resumen','')) ?></div>
                <div style="margin-top:auto">
                  <?php if (trim(val($c,'detalle',''))!==''): ?>
                    <button class="btn" data-open="<?= h($mid) ?>">Leer más</button>
                  <?php endif; ?>
                </div>
              </div>
            </article>

            <!-- Modal por item -->
            <div id="<?= h($mid) ?>" class="modal" aria-hidden="true">
              <div class="m-box">
                <div class="m-close" data-close="<?= h($mid) ?>">&times;</div>
                <h3 style="margin:0 0 10px 0"><?= h(val($c,'titulo','')) ?></h3>
                <?php if($ci = val($c,'imagen')): ?>
                  <div class="m-media"><img src="/<?= h($ci) ?>" alt=""></div>
                <?php endif; ?>
                <div class="lead" style="text-align:justify"><?= h(val($c,'detalle','')) ?></div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </section>

    <?php elseif ($tipo==='multiformato'): ?>
      <section id="<?= h($id) ?>" class="section" style="background:<?= h(val($s,'color_fondo','#ffffff')) ?>;border-width:<?= $border ?>px">
        <h2><?= h($tit) ?></h2>
        <?php 
        $bloques = val($s, 'bloques', []);
        foreach ($bloques as $bloque):
          $tipoBloque = val($bloque, 'tipo', 'texto');
          $contenido = val($bloque, 'contenido', '');
        ?>
          <?php if ($tipoBloque === 'texto'): ?>
            <div class="lead" style="margin: 20px 0;"><?= nl2br(h($contenido)) ?></div>
            
          <?php elseif ($tipoBloque === 'titulo'): ?>
            <h3 style="margin: 25px 0 15px 0;"><?= h($contenido) ?></h3>
            
          <?php elseif ($tipoBloque === 'lista'): ?>
            <ul style="margin: 20px 0; padding-left: 25px;">
              <?php if (is_array($contenido)): ?>
                <?php foreach ($contenido as $item): ?>
                  <li style="margin-bottom: 8px;"><?= h($item) ?></li>
                <?php endforeach; ?>
              <?php endif; ?>
            </ul>
            
          <?php elseif ($tipoBloque === 'imagen'): ?>
            <div style="margin: 25px 0; text-align: center;">
              <?php if (is_array($contenido['src']) && count($contenido['src']) > 0): ?>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; margin: 20px 0;">
                  <?php foreach ($contenido['src'] as $imgSrc): ?>
                    <img src="/<?= h($imgSrc) ?>" alt="<?= h($contenido['alt'] ?? '') ?>" style="width: 100%; height: 200px; object-fit: cover; border-radius: 12px; border: 1px solid var(--border);">
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
              <?php if (!empty($contenido['caption'])): ?>
                <div style="font-size: 14px; color: var(--muted); margin-top: 10px;"><?= h($contenido['caption']) ?></div>
              <?php endif; ?>
            </div>
            
          <?php elseif ($tipoBloque === 'video'): ?>
            <div style="margin: 25px 0; text-align: center;">
              <?php if (!empty($contenido['src'])): ?>
                <video width="100%" height="300" controls style="border-radius: 12px; border: 1px solid var(--border);">
                  <source src="<?= h($contenido['src']) ?>" type="video/mp4">
                  Tu navegador no soporta el elemento de video.
                </video>
                <?php if (!empty($contenido['caption'])): ?>
                  <div style="font-size: 14px; color: var(--muted); margin-top: 10px;"><?= h($contenido['caption']) ?></div>
                <?php endif; ?>
              <?php endif; ?>
            </div>
            
          <?php elseif ($tipoBloque === 'columnas'): ?>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin: 25px 0;">
              <?php if (is_array($contenido['contenido'])): ?>
                <?php foreach ($contenido['contenido'] as $colContent): ?>
                  <div style="background: var(--card); padding: 20px; border-radius: 12px; border: 1px solid var(--border);">
                    <?= nl2br(h($colContent)) ?>
                  </div>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
            
          <?php elseif ($tipoBloque === 'boton'): ?>
            <div style="text-align: center; margin: 25px 0;">
              <a href="<?= h($contenido['url'] ?? '#') ?>" class="btn" style="background: <?= h($contenido['color'] ?? '#0e7ac7') ?>; display: inline-block;">
                <?= h($contenido['texto'] ?? 'Botón') ?>
              </a>
            </div>
            
          <?php elseif ($tipoBloque === 'html'): ?>
            <div style="margin: 25px 0; padding: 20px; background: var(--card); border-radius: 12px; border: 1px solid var(--border);">
              <?= $contenido ?>
            </div>
            
          <?php endif; ?>
        <?php endforeach; ?>
      </section>
    <?php endif; ?>
  <?php endforeach; ?>

  <!-- CONTACTO + FORMULARIO (del bloque contacto + form, NO hay duplicados) -->
  <?php
    $cbg  = h(val($contacto,'bg','#ffffff'));
    $ctxt = h(val($contacto,'color_texto','#0e1525'));
    $ctit = h(val($contacto,'titulo','Contacto'));
    $cdesc= val($contacto,'contenido','');
    $fields = val($formConf,'fields',[]);
    $btxt = h(val($formConf,'button_text','Enviar'));
    $okMsg= h(val($formConf,'success_message','Gracias por contactarnos. Te responderemos pronto.'));
    $fc   = val($formConf,'colors',[]);
    $fbg  = h(val($fc,'bg','#ffffff'));
    $ftx  = h(val($fc,'text','#0e1525'));
    $fbtn = h(val($fc,'button','#0e7ac7'));
    $fbor = h(val($fc,'border','#cfd8e3'));
  ?>
  <section id="contacto" class="contact" style="background:<?= $cbg ?>;color:<?= $ctxt ?>">
    <h2 style="text-align:center"><?= $ctit ?></h2>
    <?php if($cdesc): ?><p style="text-align:center"><?= $cdesc ?></p><?php endif; ?>
    <form id="contactForm" class="c-form" style="--fb:<?= $fbor ?>;--ft:<?= $ftx ?>;--fbg:<?= $fbg ?>;--fbtn:<?= $fbtn ?>">
      <?php foreach ($fields as $f):
        $name = h(val($f,'name','campo'));
        $ph   = h(val($f,'placeholder',''));
        $type = ($name==='email')?'email':'text';
        if ($name==='mensaje'){ ?>
          <textarea name="<?= $name ?>" placeholder="<?= $ph ?>" required></textarea>
        <?php } else { ?>
          <input type="<?= $type ?>" name="<?= $name ?>" placeholder="<?= $ph ?>" required>
        <?php } endforeach; ?>
      <button class="btn" style="background:var(--fbtn)"><?= $btxt ?></button>
      <div id="contactAlert" class="alert" style="display:none"></div>
    </form>
  </section>
</div>

<?php
// Botón WhatsApp flotante (si existe)
$waUrl = '';
foreach ($socials as $s){
  $u = val($s,'url','');
  if (!$u) continue;
  if (stripos(val($s,'nombre',''), 'whats')!==false || stripos($u,'wa.me')!==false){
    $waUrl = $u; break;
  }
}
if ($waUrl):
?>
<a class="wp-float" href="<?= h($waUrl) ?>" target="_blank" rel="noopener">WhatsApp</a>
<?php endif; ?>

<footer>
  <div class="footer-inner">
    <div>📍 <?= h(val($footer,'direccion','Puerto Rico')) ?></div>
    <div>📞 <?= h(val($footer,'telefono','')) ?></div>
    <div>✉️ <a href="mailto:<?= h(val($footer,'email','')) ?>" style="color:#dbe5ff"><?= h(val($footer,'email','')) ?></a></div>
    <div class="spacer"></div>
    <small><?= h(val($footer,'texto','© SS-Group. Todos los derechos reservados.')) ?></small>
  </div>
</footer>

<!-- Modal genérico para respuesta del formulario -->
<div id="respModal" class="modal" aria-hidden="true">
  <div class="m-box">
    <div class="m-close" data-close="respModal">&times;</div>
    <div id="respMsg" class="lead"></div>
  </div>
</div>

<script>
// Abrir / cerrar modales (colección + respuesta)
document.querySelectorAll('[data-open]').forEach(el=>{
  el.addEventListener('click',()=>{
    const id = el.getAttribute('data-open');
    const m = document.getElementById(id);
    if (m) m.classList.add('show');
  });
});
document.querySelectorAll('[data-close]').forEach(el=>{
  el.addEventListener('click',()=>{
    const id = el.getAttribute('data-close');
    const m = document.getElementById(id);
    if (m) m.classList.remove('show');
  });
});
document.querySelectorAll('.modal').forEach(m=>{
  m.addEventListener('click',e=>{ if(e.target===m) m.classList.remove('show'); });
});

// Envío del formulario con fetch a form-handler.php
const f = document.getElementById('contactForm');
const alertBox = document.getElementById('contactAlert');
const respModal = document.getElementById('respModal');
const respMsg   = document.getElementById('respMsg');

if (f){
  f.addEventListener('submit', async (e)=>{
    e.preventDefault();
    alertBox.style.display='none';
    const fd = new FormData(f);
    try{
      const r = await fetch('/form-handler.php', { method:'POST', body:fd, headers:{'X-Requested-With':'XMLHttpRequest'} });
      const j = await r.json().catch(()=>({ok:false,msg:'Respuesta inválida'}));
      const ok = !!j.ok;
      const msg = j.msg || (ok ? 'Mensaje enviado.' : 'No se pudo enviar.');
      // Modal de respuesta
      respMsg.textContent = msg;
      respModal.classList.add('show');
      if (ok) f.reset();
    }catch(err){
      respMsg.textContent = 'Error de red. Intenta nuevamente.';
      respModal.classList.add('show');
    }
  });
}
</script>
</body>
</html>
