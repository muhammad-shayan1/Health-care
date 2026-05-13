<?php
include "connect.php"; 
include 'includes/head.php'; 
include 'includes/navbar.php'; 
$page_title = "Disease News & Alerts";
include 'includes/head.php';  
?>
<!-- ── Google Font ── -->
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&family=Merriweather:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
<style>
/* ── CSS Variables — portal ke blue/white theme ke saath match ── */
:root {
  --blue-dark:   #0B3D91;
  --blue-mid:    #1565C0;
  --blue-light:  #1E88E5;
  --blue-pale:   #E3F0FF;
  --blue-border: #BBDEFB;
  --white:       #FFFFFF;
  --bg:          #F4F8FF;
  --text:        #1A2740;
  --muted:       #607D9F;
  --success:     #1B8A5A;
  --danger:      #C62828;
  --warn:        #E65100;
}

/* ── Page wrapper ── */
.dn-page { background: var(--bg); min-height: 100vh; padding-bottom: 60px; }

/* ── Hero Banner ── */
.dn-hero {
  background: var(--white);
  border-bottom: 1px solid var(--blue-border);
  padding: 28px 32px;
  display: flex; align-items: center; justify-content: space-between;
  flex-wrap: wrap; gap: 16px;
}
.dn-hero h1 {
  font-family: 'Merriweather', serif;
  font-size: 26px; color: var(--blue-dark); line-height: 1.3;
}
.dn-hero h1 em { color: var(--blue-light); font-style: italic; }
.dn-hero p { font-size: 13.5px; color: var(--muted); margin-top: 5px; max-width: 500px; line-height: 1.7; }
.dn-badges { display: flex; gap: 12px; flex-wrap: wrap; }
.dn-badge {
  background: var(--blue-pale); border: 1px solid var(--blue-border);
  border-radius: 10px; padding: 12px 20px; text-align: center; min-width: 120px;
}
.dn-badge .num { font-size: 30px; font-weight: 800; color: var(--blue-mid); font-family: 'Nunito', sans-serif; }
.dn-badge .lbl { font-size: 11px; color: var(--muted); margin-top: 2px; }

/* ── Category Tabs ── */
.dn-cats {
  background: var(--white); border-bottom: 2px solid var(--blue-border);
  padding: 0 32px; display: flex; overflow-x: auto; scrollbar-width: none;
}
.dn-cats::-webkit-scrollbar { display: none; }
.dn-cat-btn {
  padding: 13px 20px; border: none; background: transparent;
  font-family: 'Nunito', sans-serif; font-size: 13.5px; font-weight: 700;
  color: var(--muted); cursor: pointer; border-bottom: 3px solid transparent;
  margin-bottom: -2px; white-space: nowrap; transition: all .2s;
  display: flex; align-items: center; gap: 6px;
}
.dn-cat-btn:hover { color: var(--blue-mid); }
.dn-cat-btn.active { color: var(--blue-mid); border-bottom-color: var(--blue-mid); }
.dn-dot { width: 8px; height: 8px; border-radius: 50%; }

/* ── Main Layout ── */
.dn-layout {
  display: grid; grid-template-columns: 1fr 290px;
  gap: 24px; max-width: 1260px; margin: 0 auto; padding: 28px 32px;
}

/* ── Generate Box ── */
.dn-gen-box {
  background: var(--white); border: 1.5px solid var(--blue-border);
  border-radius: 12px; padding: 20px; margin-bottom: 24px;
  box-shadow: 0 1px 6px rgba(21,101,192,.06);
}
.dn-gen-box h3 { font-size: 14px; color: var(--blue-dark); margin-bottom: 10px; font-family: 'Nunito', sans-serif; font-weight: 800; }
.dn-gen-row { display: flex; gap: 10px; align-items: flex-end; }
.dn-gen-box textarea {
  flex: 1; min-height: 60px; border: 1.5px solid var(--blue-border);
  border-radius: 8px; padding: 10px 12px; font-family: 'Nunito', sans-serif;
  font-size: 13.5px; color: var(--text); background: var(--bg); resize: none;
  outline: none; transition: border-color .2s;
}
.dn-gen-box textarea:focus { border-color: var(--blue-light); }
.dn-gen-box textarea::placeholder { color: var(--muted); }
.dn-gen-btn {
  padding: 10px 20px; background: var(--blue-mid); color: white; border: none;
  border-radius: 8px; font-family: 'Nunito', sans-serif; font-weight: 700;
  font-size: 13.5px; cursor: pointer; transition: background .2s, transform .1s;
  white-space: nowrap; display: flex; align-items: center; gap: 7px;
}
.dn-gen-btn:hover { background: var(--blue-dark); transform: translateY(-1px); }
.dn-gen-btn:disabled { opacity: .55; cursor: not-allowed; transform: none; }

/* ── News Cards ── */
.dn-news-list { display: flex; flex-direction: column; gap: 18px; }

.dn-card {
  background: var(--white); border: 1.5px solid var(--blue-border);
  border-radius: 12px; overflow: hidden;
  box-shadow: 0 1px 6px rgba(21,101,192,.06);
  transition: box-shadow .2s, transform .2s;
  animation: dnFadeUp .4s ease both;
}
@keyframes dnFadeUp {
  from { opacity: 0; transform: translateY(16px); }
  to   { opacity: 1; transform: translateY(0); }
}
.dn-card:hover { box-shadow: 0 5px 24px rgba(21,101,192,.13); transform: translateY(-2px); }

.dn-card-top { display: flex; border-bottom: 1px solid var(--blue-border); }
.dn-card-accent { width: 5px; min-width: 5px; flex-shrink: 0; }
.dn-card-main { padding: 20px 22px; flex: 1; }

.dn-card-header {
  display: flex; align-items: center; justify-content: space-between;
  margin-bottom: 9px; flex-wrap: wrap; gap: 7px;
}
.dn-tag-group { display: flex; align-items: center; gap: 7px; }
.dn-disease-tag {
  font-size: 11px; font-weight: 700; letter-spacing: 1px;
  text-transform: uppercase; padding: 3px 10px;
  border-radius: 100px; border: 1px solid;
  font-family: 'Nunito', sans-serif;
}
.dn-sev { font-size: 10.5px; font-weight: 700; padding: 3px 9px; border-radius: 100px; font-family: 'Nunito', sans-serif; }
.dn-sev-high { background: #FFEBEE; color: var(--danger); border: 1px solid #FFCDD2; }
.dn-sev-med  { background: #FFF3E0; color: var(--warn);   border: 1px solid #FFE0B2; }
.dn-sev-low  { background: #E8F5E9; color: var(--success);border: 1px solid #C8E6C9; }
.dn-sev-info { background: var(--blue-pale); color: var(--blue-mid); border: 1px solid var(--blue-border); }

.dn-meta-right { display: flex; align-items: center; gap: 10px; }
.dn-date   { font-size: 11.5px; color: var(--muted); }
.dn-source { font-size: 10.5px; background: var(--bg); color: var(--blue-mid); padding: 3px 9px; border-radius: 100px; border: 1px solid var(--blue-border); font-weight: 700; font-family: 'Nunito', sans-serif; }

.dn-card-title {
  font-family: 'Merriweather', serif; font-size: 17px;
  line-height: 1.4; color: var(--blue-dark); margin-bottom: 8px; cursor: pointer;
}
.dn-card-title:hover { color: var(--blue-light); }
.dn-card-excerpt { font-size: 13.5px; color: var(--muted); line-height: 1.75; font-family: 'Nunito', sans-serif; }

.dn-card-actions {
  padding: 12px 22px; display: flex; align-items: center;
  justify-content: space-between; background: var(--bg);
  flex-wrap: wrap; gap: 8px;
}
.dn-read-btn {
  background: none; border: 1.5px solid var(--blue-light);
  color: var(--blue-mid); padding: 7px 16px; border-radius: 7px;
  font-family: 'Nunito', sans-serif; font-size: 12.5px; font-weight: 700;
  cursor: pointer; transition: all .2s; display: flex; align-items: center; gap: 5px;
}
.dn-read-btn:hover { background: var(--blue-mid); color: white; border-color: var(--blue-mid); }
.dn-stats { display: flex; gap: 14px; }
.dn-stat { font-size: 11.5px; color: var(--muted); font-family: 'Nunito', sans-serif; }

/* ── Detail Panel ── */
.dn-detail {
  display: none; padding: 22px; border-top: 1.5px solid var(--blue-border);
  background: #FAFCFF;
}
.dn-detail.open { display: block; animation: dnSlide .3s ease; }
@keyframes dnSlide { from { opacity:0; transform:translateY(-6px); } to { opacity:1; transform:translateY(0); } }

.dn-detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px; }
.dn-dsec {
  background: var(--white); border: 1px solid var(--blue-border);
  border-radius: 9px; padding: 14px;
}
.dn-dsec.full { grid-column: 1 / -1; }
.dn-dsec h4 {
  font-size: 11px; font-weight: 800; text-transform: uppercase;
  letter-spacing: 1.5px; color: var(--blue-mid); margin-bottom: 9px;
  font-family: 'Nunito', sans-serif;
}
.dn-dsec p, .dn-dsec li { font-size: 13.5px; color: var(--text); line-height: 1.75; font-family: 'Nunito', sans-serif; }
.dn-dsec ul { padding-left: 16px; }
.dn-dsec li { margin-bottom: 3px; }

.dn-disclaimer {
  background: #FFF8E1; border: 1px solid #FFE082; border-radius: 7px;
  padding: 11px 14px; font-size: 12px; color: #6D4C00;
  display: flex; gap: 7px; align-items: flex-start; font-family: 'Nunito', sans-serif;
}

/* ── Sidebar ── */
.dn-sidebar { display: flex; flex-direction: column; gap: 18px; }
.dn-scard { background: var(--white); border: 1.5px solid var(--blue-border); border-radius: 12px; overflow: hidden; box-shadow: 0 1px 6px rgba(21,101,192,.06); }
.dn-scard-hdr { background: var(--blue-pale); padding: 12px 16px; font-size: 12.5px; font-weight: 800; color: var(--blue-dark); border-bottom: 1px solid var(--blue-border); font-family: 'Nunito', sans-serif; }
.dn-scard-body { padding: 12px 16px; }

.dn-alert-item { padding: 9px 0; border-bottom: 1px solid var(--blue-border); font-size: 12.5px; font-family: 'Nunito', sans-serif; }
.dn-alert-item:last-child { border-bottom: none; }
.dn-alert-item .at { font-weight: 700; color: var(--text); margin-bottom: 1px; }
.dn-alert-item .as { color: var(--muted); font-size: 11.5px; }
.dn-adot { width: 7px; height: 7px; border-radius: 50%; display: inline-block; margin-right: 5px; }

.dn-stat-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
.dn-stat-box { background: var(--bg); border: 1px solid var(--blue-border); border-radius: 8px; padding: 10px; text-align: center; }
.dn-stat-box .val { font-size: 20px; font-weight: 800; color: var(--blue-mid); font-family: 'Nunito', sans-serif; }
.dn-stat-box .lbl { font-size: 10.5px; color: var(--muted); margin-top: 1px; font-family: 'Nunito', sans-serif; }

.dn-qlink { display: flex; align-items: center; gap: 9px; padding: 9px 0; border-bottom: 1px solid var(--blue-border); text-decoration: none; color: var(--text); font-size: 12.5px; font-weight: 600; transition: color .15s; font-family: 'Nunito', sans-serif; }
.dn-qlink:last-child { border-bottom: none; }
.dn-qlink:hover { color: var(--blue-mid); }
.dn-qlico { font-size: 16px; width: 30px; height: 30px; background: var(--blue-pale); border-radius: 7px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }

/* ── Loading Dots ── */
.ldots { display: inline-flex; gap: 3px; align-items: center; }
.ldots span { width: 5px; height: 5px; border-radius: 50%; animation: ld 1s infinite; }
.ldots span:nth-child(2) { animation-delay: .15s; }
.ldots span:nth-child(3) { animation-delay: .3s;  }
@keyframes ld { 0%,80%,100%{transform:scale(.5);opacity:.4} 40%{transform:scale(1);opacity:1} }
.ldots-white span { background: white; }
.ldots-blue  span { background: var(--blue-mid); }

/* ── Error Box ── */
.dn-error { background: #FFEBEE; border: 1px solid #FFCDD2; border-radius: 8px; padding: 12px 16px; color: var(--danger); font-size: 13px; font-family: 'Nunito', sans-serif; margin-bottom: 14px; display: none; }

/* ── Responsive ── */
@media (max-width: 900px) {
  .dn-layout { grid-template-columns: 1fr; padding: 18px; }
  .dn-sidebar { display: none; }
  .dn-hero { padding: 20px; }
  .dn-cats { padding: 0 18px; }
  .dn-detail-grid { grid-template-columns: 1fr; }
  .dn-dsec.full { grid-column: 1; }
}
</style>
<?php
include "crousel.php";
?>
<!-- ══════════════════════════════════════════════════════════
     PAGE START
════════════════════════════════════════════════════════════ -->
<div class="dn-page">

  <!-- Hero Banner -->
  <div class="dn-hero">
    <div>
      <h1>Disease News &amp; <em>Clinical Updates</em></h1>
      <p>Latest disease outbreaks, research breakthroughs, and health advisories from our medical team and global health authorities.</p>
    </div>
    <div class="dn-badges">
      <div class="dn-badge">
        <div class="num" id="dn-article-count">6</div>
        <div class="lbl">Articles Today</div>
      </div>
      <div class="dn-badge" style="background:#FFF3E0;border-color:#FFE0B2;">
        <div class="num" style="color:var(--warn)">2</div>
        <div class="lbl">Active Alerts</div>
      </div>
    </div>
  </div>

  <!-- Category Tabs -->
  <div class="dn-cats">
    <button class="dn-cat-btn active" onclick="dnFilterCat(this,'all')"><span class="dn-dot" style="background:#1565C0"></span>All Updates</button>
    <button class="dn-cat-btn" onclick="dnFilterCat(this,'Infectious')"><span class="dn-dot" style="background:#C62828"></span>Infectious</button>
    <button class="dn-cat-btn" onclick="dnFilterCat(this,'Chronic')"><span class="dn-dot" style="background:#E65100"></span>Chronic</button>
    <button class="dn-cat-btn" onclick="dnFilterCat(this,'Outbreak')"><span class="dn-dot" style="background:#6A1B9A"></span>Outbreak Alert</button>
    <button class="dn-cat-btn" onclick="dnFilterCat(this,'Research')"><span class="dn-dot" style="background:#1B5E20"></span>Research</button>
    <button class="dn-cat-btn" onclick="dnFilterCat(this,'Vaccination')"><span class="dn-dot" style="background:#0277BD"></span>Vaccination</button>
  </div>

  <!-- Main Layout -->
  <div class="dn-layout">

    <!-- LEFT: News Feed -->
    <div>
      <!-- AI Generate Box -->
      <div class="dn-gen-box">
        <h3>🔍 Search Latest Disease </h3>
        <div class="dn-gen-row">
          <textarea id="dn-topic-input" placeholder="Disease ya topic likhein… e.g. 'Dengue fever 2025' ya 'Type 2 Diabetes treatment'"></textarea>
          <button class="dn-gen-btn" id="dn-gen-btn" onclick="dnGenerateNews()">
            🤖 Search News
          </button>
        </div>
        <div class="dn-error" id="dn-error-box"></div>
      </div>

      <!-- Cards List -->
      <div class="dn-news-list" id="dn-news-list"></div>
    </div>

    <!-- RIGHT: Sidebar -->
    <aside class="dn-sidebar">

      <!-- Active Alerts -->
      <div class="dn-scard">
        <div class="dn-scard-hdr">🚨 Active Health Alerts</div>
        <div class="dn-scard-body">
          <?php
          // Agar tumhari DB mein alerts table ho to yahan se load karo
          // Abhi static data hai
          $alerts = [
            ['title'=>'Dengue — High Risk',    'sub'=>'Karachi South · Updated 2h ago', 'color'=>'#C62828'],
            ['title'=>'Seasonal Flu — Moderate','sub'=>'City-wide · Updated 5h ago',     'color'=>'#E65100'],
            ['title'=>'COVID-19 — Stable',     'sub'=>'No new variants · Updated 1d ago','color'=>'#1B8A5A'],
          ];
          foreach($alerts as $a): ?>
            <div class="dn-alert-item">
              <div class="at"><span class="dn-adot" style="background:<?= $a['color'] ?>"></span><?= htmlspecialchars($a['title']) ?></div>
              <div class="as"><?= htmlspecialchars($a['sub']) ?></div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- City Health Stats -->
      <div class="dn-scard">
        <div class="dn-scard-hdr">📊 City Health Stats</div>
        <div class="dn-scard-body">
          <?php
          // DB se real data lo agar available ho
          $opd_count = 1284; // mysqli_fetch_assoc(...)['count'] etc.
          ?>
          <div class="dn-stat-grid">
            <div class="dn-stat-box"><div class="val"><?= $opd_count ?></div><div class="lbl">OPD Today</div></div>
            <div class="dn-stat-box"><div class="val">98%</div><div class="lbl">Bed Capacity</div></div>
            <div class="dn-stat-box"><div class="val">47</div><div class="lbl">ICU Active</div></div>
            <div class="dn-stat-box"><div class="val">12min</div><div class="lbl">Avg Wait</div></div>
          </div>
        </div>
      </div>

      <!-- Quick Links -->
      <div class="dn-scard">
        <div class="dn-scard-hdr">⚡ Quick Links</div>
        <div class="dn-scard-body">
          <a class="dn-qlink" href="appointment.php"><span class="dn-qlico">📅</span> Book Appointment</a>
          <a class="dn-qlink" href="lab_reports.php"><span class="dn-qlico">🧪</span> View Lab Results</a>
          <a class="dn-qlink" href="pharmacy.php"><span class="dn-qlico">💊</span> Pharmacy Portal</a>
          <a class="dn-qlink" href="telemedicine.php"><span class="dn-qlico">📞</span> Telemedicine</a>
          <a class="dn-qlink" href="emergency.php"><span class="dn-qlico">🚑</span> Emergency Triage</a>
        </div>
      </div>

      <!-- WHO Advisories -->
      <div class="dn-scard">
        <div class="dn-scard-hdr">🌐 WHO Global Advisories</div>
        <div class="dn-scard-body" style="font-size:12.5px;color:var(--muted);line-height:1.9;font-family:'Nunito',sans-serif;">
          • Mpox: Clade Ib spread monitoring<br>
          • AMR: Antibiotic resistance alert<br>
          • Cholera: India &amp; Bangladesh watch<br>
          • Avian Flu H5N1: Low human risk
        </div>
      </div>

    </aside>
  </div><!-- /dn-layout -->
</div><!-- /dn-page -->

<!-- ══════════════════════════════════════════════════════════
     JAVASCRIPT — API proxy ke zariye secure calls
════════════════════════════════════════════════════════════ -->
<script>
/* ── Data & State ─────────────────────────────────────────── */
const DN_CAT_COLORS = {
  'Infectious': { bg:'#FFEBEE', color:'#C62828', border:'#FFCDD2', accent:'#C62828' },
  'Chronic':    { bg:'#FFF3E0', color:'#E65100', border:'#FFE0B2', accent:'#E65100' },
  'Outbreak':   { bg:'#F3E5F5', color:'#6A1B9A', border:'#E1BEE7', accent:'#6A1B9A' },
  'Research':   { bg:'#E8F5E9', color:'#1B5E20', border:'#C8E6C9', accent:'#1B5E20' },
  'Vaccination':{ bg:'#E3F2FD', color:'#0277BD', border:'#BBDEFB', accent:'#0277BD' },
};
const DN_SEV_MAP = { 'High':'dn-sev-high', 'Moderate':'dn-sev-med', 'Low':'dn-sev-low', 'Info':'dn-sev-info' };

let dnCards = [];
let dnFilter = 'all';

// Default disease articles
const dnDefaults = [
  { tag:'Infectious', severity:'High',    title:'Dengue Fever Cases Surge 40% in Coastal Urban Areas — Monsoon Season Alert', excerpt:'Health authorities report a sharp spike in confirmed dengue cases across major coastal cities. Aedes aegypti mosquito populations have increased following prolonged monsoon rains, raising epidemic concerns.', source:'WHO' },
  { tag:'Chronic',    severity:'Moderate',title:'New Research Links Ultra-Processed Food to Accelerated Type 2 Diabetes Onset', excerpt:'A multi-country cohort study tracking 180,000 adults over 12 years establishes a strong dose-response relationship between ultra-processed food intake and early-onset Type 2 Diabetes, independent of BMI.', source:'Lancet' },
  { tag:'Outbreak',   severity:'High',    title:'Cholera Outbreak Confirmed in 3 Districts — Emergency Response Activated', excerpt:'District health officers have confirmed 214 cholera cases in three adjacent districts, with contaminated water supply identified as the primary vector. Emergency ORS stations are being deployed.', source:'NCDC' },
  { tag:'Research',   severity:'Info',    title:'Drug-Resistant TB Breakthrough: New Drug Combination Shows 95% Efficacy', excerpt:'Phase III trial results show a novel 4-drug regimen achieves 95% success in XDR-TB cases. The findings could reshape global TB elimination goals and WHO treatment guidelines.', source:'NEJM' },
  { tag:'Vaccination',severity:'Low',     title:'Updated Flu Vaccine 2025–26 Now Available — High-Risk Groups Urged to Vaccinate', excerpt:'The seasonal influenza vaccine has been updated to include coverage against the dominant H3N2 variant. Physicians recommend early vaccination for elderly patients and immunocompromised individuals.', source:'CDC' },
  { tag:'Infectious', severity:'Moderate',title:'Hepatitis C Elimination Campaign Achieves Milestone: 500,000 Patients Treated', excerpt:'The national Hepatitis C elimination program has surpassed its halfway target, with universal DAA therapy access transforming outcomes and reducing liver cancer incidence by 18%.', source:'WHO' },
];

/* ── Secure API Call (via PHP proxy) ─────────────────────── */
async function dnCallProxy(payload) {
  const res = await fetch('api_proxy.php', {         // ← PHP proxy
    method:  'POST',
    headers: { 'Content-Type': 'application/json' },
    body:    JSON.stringify(payload)
  });
  if (!res.ok) {
    const err = await res.json().catch(() => ({}));
    throw new Error(err.error || 'Server error');
  }
  return await res.json();
}

/* ── Show Error ───────────────────────────────────────────── */
function dnShowError(msg) {
  const box = document.getElementById('dn-error-box');
  box.textContent = '⚠ ' + msg;
  box.style.display = 'block';
  setTimeout(() => { box.style.display = 'none'; }, 5000);
}

/* ── Render Cards ─────────────────────────────────────────── */
function dnRender() {
  const list = document.getElementById('dn-news-list');
  document.getElementById('dn-article-count').textContent = dnCards.length;

  const filtered = dnFilter === 'all' ? dnCards : dnCards.filter(c => c.tag === dnFilter);

  if (!filtered.length) {
    list.innerHTML = `<div style="text-align:center;padding:50px 20px;color:var(--muted);">
      <div style="font-size:38px;margin-bottom:10px;">🔍</div>
      <h3 style="color:var(--blue-dark);font-family:'Merriweather',serif;margin-bottom:6px;">No articles found</h3>
      <p style="font-family:'Nunito',sans-serif;font-size:13.5px;">Try a different category or generate new articles above.</p>
    </div>`;
    return;
  }

  const today = new Date().toLocaleDateString('en-PK', { day:'numeric', month:'short', year:'numeric' });

  list.innerHTML = filtered.map((card, i) => {
    const cat    = DN_CAT_COLORS[card.tag] || { bg:'var(--blue-pale)', color:'var(--blue-mid)', border:'var(--blue-border)', accent:'var(--blue-mid)' };
    const sevCls = DN_SEV_MAP[card.severity] || 'dn-sev-info';
    const src    = card.source || 'WHO';
    const views  = Math.floor(Math.random() * 900 + 150);

    return `
    <div class="dn-card" style="animation-delay:${i * .06}s" id="dncard-${card.id}">
      <div class="dn-card-top">
        <div class="dn-card-accent" style="background:${cat.accent}"></div>
        <div class="dn-card-main">
          <div class="dn-card-header">
            <div class="dn-tag-group">
              <span class="dn-disease-tag" style="background:${cat.bg};color:${cat.color};border-color:${cat.border}">${card.tag}</span>
              <span class="dn-sev ${sevCls}">⚠ ${card.severity}</span>
            </div>
            <div class="dn-meta-right">
              <span class="dn-source">${src}</span>
              <span class="dn-date">📅 ${today}</span>
            </div>
          </div>
          <div class="dn-card-title" onclick="dnToggleDetail(${card.id})">${card.title}</div>
          <div class="dn-card-excerpt">${card.excerpt}</div>
        </div>
      </div>
      <div class="dn-card-actions">
        <button class="dn-read-btn" id="dnbtn-${card.id}" onclick="dnToggleDetail(${card.id})">
          ${card.isLoading
            ? `<span class="ldots ldots-blue"><span></span><span></span><span></span></span> Loading...`
            : card.detailOpen ? '▲ Hide Clinical Details' : '📋 View Clinical Details'
          }
        </button>
        <div class="dn-stats">
          <span class="dn-stat">👁 ${views} views</span>
          <span class="dn-stat">🔗 Share</span>
          <span class="dn-stat">🖨 Print</span>
        </div>
      </div>
      <div class="dn-detail ${card.detailOpen ? 'open' : ''}" id="dndetail-${card.id}">
        ${card.detail || ''}
      </div>
    </div>`;
  }).join('');
}

/* ── Toggle Detail ───────────────────────────────────────── */
async function dnToggleDetail(id) {
  const card = dnCards.find(c => c.id === id);
  if (!card) return;

  // Already loaded — toggle open/close
  if (card.detail) {
    card.detailOpen = !card.detailOpen;
    dnRender();
    return;
  }

  // Load from proxy
  card.isLoading = true;
  dnRender();

  try {
    const result = await dnCallProxy({ type: 'get_detail', title: card.title });
    if (!result.success) throw new Error(result.error || 'Failed');
    card.detail = dnBuildDetail(result.data);
  } catch (e) {
    card.detail = `<p style="color:var(--muted);padding:10px;font-family:'Nunito',sans-serif;">Could not load details: ${e.message}</p>`;
  }

  card.isLoading  = false;
  card.detailOpen = true;
  dnRender();
}

/* ── Build Detail HTML ───────────────────────────────────── */
function dnBuildDetail(d) {
  const symList  = (d.symptoms  || []).map(s => `<li>${s}</li>`).join('');
  const prevList = (d.prevention|| []).map(p => `<li>${p}</li>`).join('');
  return `
  <div class="dn-detail-grid">
    <div class="dn-dsec full"><h4>📋 Clinical Overview</h4><p>${d.overview || ''}</p></div>
    <div class="dn-dsec"><h4>🩺 Key Symptoms</h4><ul>${symList}</ul></div>
    <div class="dn-dsec"><h4>⚗️ Causes &amp; Risk Factors</h4><p>${d.causes || ''}</p></div>
    <div class="dn-dsec"><h4>💊 Treatment Protocol</h4><p>${d.treatment || ''}</p></div>
    <div class="dn-dsec"><h4>🛡️ Prevention Guidelines</h4><ul>${prevList}</ul></div>
    <div class="dn-dsec full"><h4>🚨 When to Visit Hospital</h4><p>${d.when_to_visit || ''}</p></div>
  </div>
  <div class="dn-disclaimer">
    ⚕️ <span>For educational purposes only. Always consult a qualified physician for diagnosis or treatment.</span>
  </div>`;
}

/* ── Generate News ───────────────────────────────────────── */
async function dnGenerateNews() {
  const topic = document.getElementById('dn-topic-input').value.trim();
  if (!topic) { dnShowError('Please enter a disease or health topic.'); return; }

  const btn = document.getElementById('dn-gen-btn');
  btn.disabled = true;
  btn.innerHTML = '<span class="ldots ldots-white"><span></span><span></span><span></span></span> Generating...';

  try {
    const result = await dnCallProxy({ type: 'generate_news', topic: topic });
    if (!result.success) throw new Error(result.error || 'Failed');

    const items = Array.isArray(result.data) ? result.data : [];
    items.forEach(t => {
      dnCards.unshift({ ...t, id: Date.now() + Math.random(), detail: null, detailOpen: false, isLoading: false });
    });
    dnRender();
    document.getElementById('dn-topic-input').value = '';
  } catch (e) {
    dnShowError(e.message || 'Could not generate articles. Please try again.');
  }

  btn.disabled = false;
  btn.innerHTML = '🤖 Generate Report';
}

/* ── Filter Category ─────────────────────────────────────── */
function dnFilterCat(btn, cat) {
  document.querySelectorAll('.dn-cat-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  dnFilter = cat;
  dnRender();
}

/* ── Init ────────────────────────────────────────────────── */
dnDefaults.forEach((c, i) => {
  dnCards.push({ ...c, id: i + 1, detail: null, detailOpen: false, isLoading: false });
});
dnRender();
</script>
<?php include 'includes/footer.php'; ?>
<?php include 'includes/scripts.php'; ?>
