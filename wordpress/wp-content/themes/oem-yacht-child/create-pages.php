<?php
/**
 * Script to create all OEM Yacht Service pages via WP-CLI eval-file.
 * Run: wp eval-file /path/to/create-pages.php --allow-root
 */

require_once get_stylesheet_directory() . '/functions.php';

$services  = oem_get_services();
$projects  = oem_get_projects();
$team_data = oem_get_team();
$vacancies = oem_get_vacancies();
$locations = oem_get_locations();

function create_page($title, $slug, $content, $parent_id = 0) {
    $existing = get_page_by_path($slug);
    if ($existing) {
        wp_update_post([
            'ID'           => $existing->ID,
            'post_title'   => $title,
            'post_content' => $content,
            'post_status'  => 'publish',
            'post_parent'  => $parent_id,
        ]);
        echo "Updated: $slug (ID: {$existing->ID})\n";
        return $existing->ID;
    }
    $id = wp_insert_post([
        'post_title'   => $title,
        'post_name'    => $slug,
        'post_content' => $content,
        'post_status'  => 'publish',
        'post_type'    => 'page',
        'post_parent'  => $parent_id,
    ]);
    echo "Created: $slug (ID: $id)\n";
    return $id;
}

// ═══════════════════════════════════════════════════
// HOME PAGE
// ═══════════════════════════════════════════════════

$home_services_cards = '';
foreach ($services as $s) {
    $home_services_cards .= '
    <a href="/what-we-do/' . $s['slug'] . '/" class="oem-service-card">
      <div class="oem-service-card__icon"><i class="fa-solid ' . $s['icon'] . '"></i></div>
      <h3 class="oem-service-card__title">' . $s['title'] . '</h3>
      <p class="oem-service-card__desc">' . $s['short'] . '</p>
      <span class="oem-service-card__link">Read more <i class="fa-solid fa-arrow-right"></i></span>
    </a>';
}

$home_project_cards = '';
for ($i = 0; $i < 4; $i++) {
    $p = $projects[$i];
    $home_project_cards .= '
    <a href="/projects/' . $p['slug'] . '/" class="oem-project-card">
      <div class="oem-project-card__image"><i class="fa-solid fa-image"></i></div>
      <div class="oem-project-card__tag">' . $p['tag'] . '</div>
      <h3 class="oem-project-card__title">' . $p['title'] . '</h3>
    </a>';
}

$home_content = '
<!-- HERO -->
<section class="oem-hero">
  <video class="oem-hero__video" autoplay muted loop playsinline>
    <source src="/wp-content/themes/oem-yacht-child/assets/hero-bg.mov" type="video/quicktime">
  </video>
  <div class="oem-hero__overlay"></div>
  <div class="oem-hero__content">
    <div class="oem-eyebrow">OEM Yacht Service</div>
    <h1 class="oem-h1" style="color:#fff;max-width:760px;">Independent yacht support. <span class="oem-red">OEM-grade</span> knowledge.</h1>
    <p class="oem-hero__subtitle">Service, refit and control technology for superyachts, delivered from Bolsward, Barcelona and Fort Lauderdale.</p>
    <div class="oem-hero__buttons">
      <a href="/contact/" class="oem-btn oem-btn--red">Request a survey <i class="fa-solid fa-arrow-right"></i></a>
      <a href="/oem-connect/" class="oem-btn oem-btn--outline">OEM Connect</a>
    </div>
  </div>
</section>

<!-- PILLARS -->
<div class="oem-section">
  <div class="oem-pillars">
    <div>
      <div class="oem-pillar__icon"><i class="fa-solid fa-anchor"></i></div>
      <h3 class="oem-pillar__title">Only Service &amp; Refit</h3>
      <p class="oem-pillar__desc">No new-build distractions. Every engineer, every tool and every hour is dedicated to keeping existing yachts running.</p>
    </div>
    <div>
      <div class="oem-pillar__icon"><i class="fa-solid fa-layer-group"></i></div>
      <h3 class="oem-pillar__title">One Stop Shop</h3>
      <p class="oem-pillar__desc">Hydraulics, electrics, mechanics, cranes, boarding and hull doors — one team, one contract, one responsibility.</p>
    </div>
    <div>
      <div class="oem-pillar__icon"><i class="fa-solid fa-signal"></i></div>
      <h3 class="oem-pillar__title">OEM Connect</h3>
      <p class="oem-pillar__desc">Live diagnostics and remote support via our secure platform — reducing downtime and unplanned yard visits.</p>
    </div>
  </div>
</div>

<!-- WHAT WE DO -->
<section style="background:var(--oem-paper);padding:80px 0;">
  <div class="oem-section">
    <div class="oem-eyebrow">01 — What we do</div>
    <h2 class="oem-h2" style="max-width:600px;margin-bottom:40px;">Six disciplines, one service organisation.</h2>
    <div class="oem-card-grid">' . $home_services_cards . '
    </div>
  </div>
</section>

<!-- PROJECTS -->
<section style="padding:80px 0;">
  <div class="oem-section">
    <div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:40px;flex-wrap:wrap;gap:16px;">
      <div>
        <div class="oem-eyebrow">02 — Projects</div>
        <h2 class="oem-h2">Selected work.</h2>
      </div>
      <a href="/projects/" class="oem-service-card__link">All projects <i class="fa-solid fa-arrow-right"></i></a>
    </div>
    <div class="oem-card-grid oem-card-grid--4">' . $home_project_cards . '
    </div>
  </div>
</section>

<!-- OEM CONNECT PROMO -->
<section style="background:var(--oem-ink);padding:80px 0;color:#fff;">
  <div class="oem-section">
    <div class="oem-two-col--connect" style="display:grid;grid-template-columns:1.15fr 0.85fr;gap:48px;align-items:center;">
      <div>
        <div class="oem-eyebrow">OEM Connect</div>
        <h2 class="oem-h2" style="color:#fff;">Remote diagnostics. Predictive maintenance.</h2>
        <p style="font:300 18px/1.55 \'Source Sans 3\',sans-serif;color:rgba(255,255,255,.82);max-width:480px;margin:0 0 28px;">Our secure platform connects your on-board equipment to our engineering team — live sensor data, remote PLC access and a complete digital service history per serial number.</p>
        <a href="/oem-connect/" class="oem-btn oem-btn--red">Explore OEM Connect <i class="fa-solid fa-arrow-right"></i></a>
      </div>
      <div class="oem-stats">
        <div class="oem-stat"><p class="oem-stat__value">3 bases</p><p class="oem-stat__label">Bolsward · Barcelona · Fort Lauderdale</p></div>
        <div class="oem-stat"><p class="oem-stat__value">&lt; 1 day</p><p class="oem-stat__label">Average remote-response time</p></div>
        <div class="oem-stat"><p class="oem-stat__value">Per serial</p><p class="oem-stat__label">Full service history by equipment serial</p></div>
      </div>
    </div>
  </div>
</section>';

$home_id = create_page('Home', 'home', $home_content);
update_option('show_on_front', 'page');
update_option('page_on_front', $home_id);

// ═══════════════════════════════════════════════════
// WHAT WE DO - INDEX
// ═══════════════════════════════════════════════════

$wwd_cards = '';
foreach ($services as $s) {
    $wwd_cards .= '
    <a href="/what-we-do/' . $s['slug'] . '/" class="oem-service-card">
      <div class="oem-service-card__icon"><i class="fa-solid ' . $s['icon'] . '"></i></div>
      <h3 class="oem-service-card__title">' . $s['title'] . '</h3>
      <p class="oem-service-card__desc">' . $s['short'] . '</p>
      <span class="oem-service-card__link">Read more <i class="fa-solid fa-arrow-right"></i></span>
    </a>';
}

$wwd_content = '
<section class="oem-page-header oem-page-header--paper">
  <div class="oem-section">
    <div class="oem-eyebrow">What we do</div>
    <h1 class="oem-h1 oem-h1--page">Six disciplines, one service organisation.</h1>
    <p class="oem-intro">Hydraulics, electrics, mechanics, cranes, boarding equipment and hull doors — maintained, repaired and upgraded by OEM-trained engineers from three bases worldwide.</p>
  </div>
</section>
<section class="oem-content-section">
  <div class="oem-section">
    <div class="oem-card-grid">' . $wwd_cards . '
    </div>
  </div>
</section>';

$wwd_id = create_page('What We Do', 'what-we-do', $wwd_content);

// ═══════════════════════════════════════════════════
// WHAT WE DO - DETAIL PAGES (×6)
// ═══════════════════════════════════════════════════

foreach ($services as $idx => $s) {
    $scope_html = '';
    foreach ($s['scope'] as $item) {
        $scope_html .= '<li>' . $item . '</li>';
    }

    $specs_html = '';
    foreach ($s['specs'] as $spec) {
        $specs_html .= '<tr><td>' . $spec['k'] . '</td><td>' . $spec['v'] . '</td></tr>';
    }

    $related = array_values(array_filter($services, fn($x) => $x['slug'] !== $s['slug']));
    $related = array_slice($related, 0, 3);
    $related_html = '';
    foreach ($related as $r) {
        $related_html .= '
        <a href="/what-we-do/' . $r['slug'] . '/" class="oem-service-card">
          <div class="oem-service-card__icon"><i class="fa-solid ' . $r['icon'] . '"></i></div>
          <h3 class="oem-service-card__title">' . $r['title'] . '</h3>
          <p class="oem-service-card__desc">' . $r['short'] . '</p>
          <span class="oem-service-card__link">Read more <i class="fa-solid fa-arrow-right"></i></span>
        </a>';
    }

    $detail_content = '
<section class="oem-page-header oem-page-header--paper">
  <div class="oem-section">
    <p class="oem-breadcrumb"><a href="/what-we-do/">What we do</a> / <span>' . $s['title'] . '</span></p>
    <div class="oem-two-col--equal" style="display:grid;grid-template-columns:1fr 1fr;gap:48px;align-items:start;">
      <div>
        <div class="oem-eyebrow">' . $s['eyebrow'] . '</div>
        <h1 class="oem-h1 oem-h1--detail">' . $s['headline'] . '</h1>
        <p class="oem-intro" style="font-size:18px;line-height:1.55;">' . $s['intro'] . '</p>
      </div>
      <div class="oem-image-placeholder"><i class="fa-solid fa-image"></i></div>
    </div>
  </div>
</section>

<section class="oem-content-section">
  <div class="oem-section">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:48px;">
      <div>
        <h2 style="font:700 26px/1.2 \'Source Sans 3\',sans-serif;margin:0 0 20px;">Scope of work</h2>
        <ul class="oem-scope-list">' . $scope_html . '</ul>
      </div>
      <div>
        <h2 style="font:700 26px/1.2 \'Source Sans 3\',sans-serif;margin:0 0 20px;">Specification</h2>
        <table class="oem-spec-table">' . $specs_html . '</table>
      </div>
    </div>
  </div>
</section>

<section style="background:var(--oem-paper);padding:64px 0;">
  <div class="oem-section">
    <h2 class="oem-h2--sm" style="font:700 clamp(26px,3vw,36px)/1.15 \'Source Sans 3\',sans-serif;margin:0 0 32px;">Also in What we do</h2>
    <div class="oem-card-grid">' . $related_html . '</div>
  </div>
</section>';

    create_page($s['title'], $s['slug'], $detail_content, $wwd_id);
}

// ═══════════════════════════════════════════════════
// OEM CONNECT
// ═══════════════════════════════════════════════════

$connect_features = [
    ['icon' => 'fa-signal',         'title' => 'Live diagnostics',      'desc' => 'Real-time sensor data streamed from on-board PLCs to our engineering dashboard — pressure, temperature, position, runtime hours.'],
    ['icon' => 'fa-microchip',      'title' => 'Remote logic updates',  'desc' => 'We push PLC software changes over a secure tunnel — no on-board visit required for parameter tweaks, sequence adjustments or firmware patches.'],
    ['icon' => 'fa-clipboard-list', 'title' => 'Service history',       'desc' => 'Every intervention, every spare part and every test result logged against the equipment serial number — accessible to crew and management.'],
    ['icon' => 'fa-wrench',         'title' => 'Direct line',           'desc' => 'One tap connects the crew to the engineer who last worked on that system — no call centres, no ticket queues.'],
];
$feat_html = '';
foreach ($connect_features as $f) {
    $feat_html .= '
    <div class="oem-feature">
      <div class="oem-feature__icon"><i class="fa-solid ' . $f['icon'] . '"></i></div>
      <h3 class="oem-feature__title">' . $f['title'] . '</h3>
      <p class="oem-feature__desc">' . $f['desc'] . '</p>
    </div>';
}

$connect_steps = [
    ['n' => '01', 'title' => 'Crew reports',  'desc' => 'The crew taps a single button on the bridge panel or the OEM Connect app to flag a system.'],
    ['n' => '02', 'title' => 'We read out',   'desc' => 'Our engineer opens a secure tunnel to the vessel\'s PLC and reads live diagnostics within minutes.'],
    ['n' => '03', 'title' => 'Fix or plan',   'desc' => 'If it\'s a parameter or logic issue, we push a fix remotely. If hardware is needed, we dispatch a team with the right parts.'],
    ['n' => '04', 'title' => 'Logged',        'desc' => 'Every action — remote or on-board — is logged against the equipment serial in the vessel\'s digital service history.'],
];
$steps_html = '';
foreach ($connect_steps as $st) {
    $steps_html .= '
    <div class="oem-step">
      <div class="oem-step__num">' . $st['n'] . '</div>
      <h3 class="oem-step__title">' . $st['title'] . '</h3>
      <p class="oem-step__desc">' . $st['desc'] . '</p>
    </div>';
}

$connect_content = '
<section class="oem-page-header oem-page-header--dark" style="position:relative;padding:80px 0;">
  <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;overflow:hidden;">
    <img src="' . get_stylesheet_directory_uri() . '/assets/logo-watermark.svg" alt="" style="width:400px;opacity:.06;">
  </div>
  <div class="oem-section" style="position:relative;">
    <div class="oem-eyebrow" style="color:var(--oem-red);">OEM Connect</div>
    <h1 class="oem-h1 oem-h1--page" style="color:#fff;max-width:700px;">Remote diagnostics. Predictive maintenance. One platform.</h1>
    <p class="oem-intro" style="color:rgba(255,255,255,.82);">Our secure platform connects your on-board equipment to our engineering team — live sensor data, remote PLC access and a complete digital service history per serial number.</p>
  </div>
</section>

<section class="oem-content-section">
  <div class="oem-section">
    <div class="oem-card-grid" style="gap:32px;">' . $feat_html . '
    </div>
  </div>
</section>

<section style="background:var(--oem-paper);padding:64px 0;">
  <div class="oem-section">
    <h2 class="oem-h2" style="margin-bottom:40px;">How a service call runs</h2>
    <div class="oem-steps">' . $steps_html . '</div>
  </div>
</section>';

create_page('OEM Connect', 'oem-connect', $connect_content);

// ═══════════════════════════════════════════════════
// PROJECTS - INDEX
// ═══════════════════════════════════════════════════

$proj_cards = '';
foreach ($projects as $p) {
    $proj_cards .= '
    <a href="/projects/' . $p['slug'] . '/" class="oem-project-card">
      <div class="oem-project-card__image"><i class="fa-solid fa-image"></i></div>
      <div class="oem-project-card__tag">' . $p['tag'] . '</div>
      <h3 class="oem-project-card__title">' . $p['title'] . '</h3>
      <p class="oem-project-card__vessel">' . $p['vessel'] . '</p>
    </a>';
}

$proj_index_content = '
<section class="oem-page-header oem-page-header--paper">
  <div class="oem-section">
    <div class="oem-eyebrow">Projects</div>
    <h1 class="oem-h1 oem-h1--page">Selected work.</h1>
    <p class="oem-intro">A cross-section of recent service, refit and new-build support projects — from single-system repairs to multi-discipline vessel programmes.</p>
  </div>
</section>
<section class="oem-content-section">
  <div class="oem-section">
    <div class="oem-card-grid">' . $proj_cards . '
    </div>
  </div>
</section>';

$proj_id = create_page('Projects', 'projects', $proj_index_content);

// ═══════════════════════════════════════════════════
// PROJECTS - DETAIL PAGES (×8)
// ═══════════════════════════════════════════════════

foreach ($projects as $p) {
    $delivered_html = '';
    foreach ($p['delivered'] as $d) {
        $delivered_html .= '<li>' . $d . '</li>';
    }

    $facts_html = '';
    foreach ($p['facts'] as $f) {
        $facts_html .= '
        <div class="oem-fact-sheet__row">
          <span class="oem-fact-sheet__key">' . $f['k'] . '</span>
          <span class="oem-fact-sheet__val">' . $f['v'] . '</span>
        </div>';
    }

    $proj_detail = '
<section class="oem-page-header" style="padding:72px 0 32px;">
  <div class="oem-section">
    <p class="oem-breadcrumb"><a href="/projects/">Projects</a> / <span>' . $p['title'] . '</span></p>
    <div class="oem-project-card__tag" style="margin-bottom:12px;">' . $p['tag'] . '</div>
    <h1 class="oem-h1 oem-h1--detail">' . $p['title'] . '</h1>
    <p style="font:400 17px/1.65 \'Source Sans 3\',sans-serif;color:var(--oem-charcoal-70);margin:8px 0 0;">' . $p['vessel'] . '</p>
  </div>
</section>

<div class="oem-section" style="margin-bottom:48px;">
  <div class="oem-image-placeholder oem-image-placeholder--hero"><i class="fa-solid fa-image" style="font-size:48px;"></i></div>
</div>

<section class="oem-content-section" style="padding-top:0;">
  <div class="oem-section">
    <div style="display:grid;grid-template-columns:1.2fr 0.8fr;gap:48px;align-items:start;">
      <div>
        <h2 style="font:700 26px/1.2 \'Source Sans 3\',sans-serif;margin:0 0 16px;">The job</h2>
        <p class="oem-body">' . $p['body'] . '</p>
        <h2 style="font:700 26px/1.2 \'Source Sans 3\',sans-serif;margin:32px 0 16px;">Delivered</h2>
        <ul class="oem-scope-list">' . $delivered_html . '</ul>
      </div>
      <div class="oem-fact-sheet">
        <h3 style="font:700 18px/1.25 \'Source Sans 3\',sans-serif;margin:0 0 16px;">Fact sheet</h3>
        ' . $facts_html . '
        <div style="margin-top:24px;">
          <a href="/contact/" class="oem-btn oem-btn--red" style="width:100%;justify-content:center;">Discuss a similar scope <i class="fa-solid fa-arrow-right"></i></a>
        </div>
      </div>
    </div>
  </div>
</section>';

    create_page($p['title'], $p['slug'], $proj_detail, $proj_id);
}

// ═══════════════════════════════════════════════════
// CAREERS
// ═══════════════════════════════════════════════════

$vac_html = '';
foreach ($vacancies as $v) {
    $vac_html .= '
    <a href="/contact/" class="oem-vacancy-row">
      <span class="oem-vacancy-row__title">' . $v['title'] . '</span>
      <span class="oem-vacancy-row__meta">' . $v['location'] . '</span>
      <span class="oem-vacancy-row__meta">' . $v['type'] . '</span>
      <span class="oem-vacancy-row__arrow"><i class="fa-solid fa-arrow-right"></i></span>
    </a>';
}

$careers_content = '
<section class="oem-page-header oem-page-header--paper">
  <div class="oem-section">
    <div class="oem-two-col--equal" style="display:grid;grid-template-columns:1fr 1fr;gap:48px;align-items:start;">
      <div>
        <div class="oem-eyebrow">Careers</div>
        <h1 class="oem-h1 oem-h1--page">Build superyacht systems. See the world.</h1>
        <p class="oem-intro">We are always looking for experienced marine engineers and technicians who want to work on the most complex yacht systems afloat — from our workshop in Bolsward or on board worldwide.</p>
      </div>
      <div class="oem-image-placeholder"><i class="fa-solid fa-image"></i></div>
    </div>
  </div>
</section>

<section class="oem-content-section">
  <div class="oem-section">
    <h2 style="font:700 26px/1.2 \'Source Sans 3\',sans-serif;margin:0 0 24px;">Open positions</h2>
    ' . $vac_html . '
  </div>
</section>

<section style="padding:48px 0 80px;">
  <div class="oem-section">
    <div class="oem-cta-banner">
      <div>
        <h3 class="oem-cta-banner__title">Don\'t see your role?</h3>
        <p class="oem-cta-banner__text">Send an open application — we\'re always interested in experienced marine engineers.</p>
      </div>
      <a href="mailto:info@oemyachtservice.com" class="oem-btn oem-btn--red">Open application <i class="fa-solid fa-arrow-right"></i></a>
    </div>
  </div>
</section>';

create_page('Careers', 'careers', $careers_content);

// ═══════════════════════════════════════════════════
// TEAM
// ═══════════════════════════════════════════════════

$team = $team_data['team'];
$team_people_html = '';
foreach ($team['people'] as $i => $person) {
    $team_people_html .= '
    <div class="oem-person">
      <div class="oem-person__photo"><i class="fa-solid fa-user"></i></div>
      <h3 class="oem-person__name">' . $person['name'] . '</h3>
      <p class="oem-person__role">' . $person['role'] . '</p>
      <p class="oem-person__base">' . $person['base'] . '</p>
    </div>';
}

$team_content = '
<section class="oem-page-header oem-page-header--paper">
  <div class="oem-section">
    <div class="oem-eyebrow">' . $team['eyebrow'] . '</div>
    <h1 class="oem-h1 oem-h1--page">' . $team['headline'] . '</h1>
    <p class="oem-intro">' . $team['intro'] . '</p>
    <div class="oem-team-tabs">
      <a href="/team/" class="oem-team-tab oem-team-tab--active">Team</a>
      <a href="/team-sub/" class="oem-team-tab">Team Sub</a>
    </div>
  </div>
</section>

<section class="oem-content-section">
  <div class="oem-section">
    <div class="oem-card-grid oem-card-grid--4">' . $team_people_html . '</div>
  </div>
</section>';

create_page('Team', 'team', $team_content);

// ═══════════════════════════════════════════════════
// TEAM SUB
// ═══════════════════════════════════════════════════

$sub = $team_data['sub'];
$sub_people_html = '';
foreach ($sub['people'] as $i => $person) {
    $sub_people_html .= '
    <div class="oem-person">
      <div class="oem-person__photo"><i class="fa-solid fa-user"></i></div>
      <h3 class="oem-person__name">' . $person['name'] . '</h3>
      <p class="oem-person__role">' . $person['role'] . '</p>
      <p class="oem-person__base">' . $person['base'] . '</p>
    </div>';
}

$sub_content = '
<section class="oem-page-header oem-page-header--paper">
  <div class="oem-section">
    <div class="oem-eyebrow">' . $sub['eyebrow'] . '</div>
    <h1 class="oem-h1 oem-h1--page">' . $sub['headline'] . '</h1>
    <p class="oem-intro">' . $sub['intro'] . '</p>
    <div class="oem-team-tabs">
      <a href="/team/" class="oem-team-tab">Team</a>
      <a href="/team-sub/" class="oem-team-tab oem-team-tab--active">Team Sub</a>
    </div>
  </div>
</section>

<section class="oem-content-section">
  <div class="oem-section">
    <div class="oem-card-grid oem-card-grid--4">' . $sub_people_html . '</div>
  </div>
</section>';

create_page('Team Sub', 'team-sub', $sub_content);

// ═══════════════════════════════════════════════════
// CONTACT
// ═══════════════════════════════════════════════════

$loc_html = '';
foreach ($locations as $loc) {
    $loc_html .= '
    <div class="oem-location">
      <h3 class="oem-location__city">' . $loc['city'] . '</h3>
      <p class="oem-location__role">' . $loc['role'] . '</p>
      <p class="oem-location__address">' . $loc['address'] . '</p>
      <a href="' . $loc['tel_href'] . '" class="oem-location__tel">' . $loc['tel'] . '</a>
    </div>';
}

$contact_content = '
<section class="oem-page-header oem-page-header--paper">
  <div class="oem-section">
    <div class="oem-eyebrow">Contact</div>
    <h1 class="oem-h1 oem-h1--page">Request a survey or get in touch.</h1>
    <p class="oem-intro">Tell us about your vessel and the scope of work — we will come back to you within one working day.</p>
  </div>
</section>

<section class="oem-content-section">
  <div class="oem-section">
    <div style="display:grid;grid-template-columns:1.15fr 0.85fr;gap:48px;align-items:start;">
      <div>
        <form class="oem-form" action="#" method="post">
          <div>
            <label>Name</label>
            <input type="text" name="name" required>
          </div>
          <div>
            <label>Email</label>
            <input type="email" name="email" required>
          </div>
          <div>
            <label>Vessel</label>
            <input type="text" name="vessel" placeholder="Yard, length">
          </div>
          <div>
            <label>Discipline</label>
            <select name="discipline">
              <option value="">Select…</option>';
foreach ($services as $s) {
    $contact_content .= '
              <option value="' . $s['slug'] . '">' . $s['title'] . '</option>';
}
$contact_content .= '
            </select>
          </div>
          <div class="full-width">
            <label>Scope</label>
            <textarea name="scope" rows="5" placeholder="Describe the work you need…"></textarea>
          </div>
          <div class="full-width">
            <button type="submit" class="oem-btn oem-btn--red">Request a survey <i class="fa-solid fa-arrow-right"></i></button>
          </div>
        </form>
      </div>
      <div>' . $loc_html . '</div>
    </div>
  </div>
</section>';

create_page('Contact', 'contact', $contact_content);

// ═══════════════════════════════════════════════════
// NAVIGATION MENU
// ═══════════════════════════════════════════════════

$menu_name = 'Primary Navigation';
$menu_exists = wp_get_nav_menu_object($menu_name);
if ($menu_exists) {
    wp_delete_nav_menu($menu_name);
}
$menu_id = wp_create_nav_menu($menu_name);

$pages_map = [];
$all_pages = get_pages();
foreach ($all_pages as $pg) {
    $pages_map[$pg->post_name] = $pg->ID;
}

function add_menu_item($menu_id, $title, $page_id, $parent = 0, $classes = []) {
    return wp_update_nav_menu_item($menu_id, 0, [
        'menu-item-title'     => $title,
        'menu-item-object'    => 'page',
        'menu-item-object-id' => $page_id,
        'menu-item-type'      => 'post_type',
        'menu-item-status'    => 'publish',
        'menu-item-parent-id' => $parent,
        'menu-item-classes'   => implode(' ', $classes),
    ]);
}

$home_menu   = add_menu_item($menu_id, 'Home', $pages_map['home'] ?? 0);
$wwd_menu    = add_menu_item($menu_id, 'What we do', $pages_map['what-we-do'] ?? 0, 0, ['menu-item-what-we-do']);

foreach ($services as $s) {
    if (isset($pages_map[$s['slug']])) {
        add_menu_item($menu_id, $s['title'], $pages_map[$s['slug']], $wwd_menu);
    }
}

add_menu_item($menu_id, 'OEM Connect', $pages_map['oem-connect'] ?? 0);

$proj_menu = add_menu_item($menu_id, 'Projects', $pages_map['projects'] ?? 0, 0, ['menu-item-projects']);
foreach ($projects as $p) {
    if (isset($pages_map[$p['slug']])) {
        add_menu_item($menu_id, $p['nav'], $pages_map[$p['slug']], $proj_menu);
    }
}

add_menu_item($menu_id, 'Careers', $pages_map['careers'] ?? 0);

$about_menu = add_menu_item($menu_id, 'About', $pages_map['team'] ?? 0, 0, ['menu-item-about']);
add_menu_item($menu_id, 'Team', $pages_map['team'] ?? 0, $about_menu);
add_menu_item($menu_id, 'Team Sub', $pages_map['team-sub'] ?? 0, $about_menu);

add_menu_item($menu_id, 'Contact', $pages_map['contact'] ?? 0);

$theme_locations = get_theme_mod('nav_menu_locations');
$theme_locations['primary'] = $menu_id;
set_theme_mod('nav_menu_locations', $theme_locations);

echo "\nMenu created and assigned.\n";

// Flush rewrite rules
flush_rewrite_rules();
echo "Done! All pages and menu created.\n";
