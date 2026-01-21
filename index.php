<?php
session_start();
$data = json_decode(file_get_contents("data.json"), true);

// Handle language switch
if (isset($_GET['lang'])) {
    $lang = $_GET['lang'] === 'en' ? 'en' : 'nl';
    $_SESSION['lang'] = $lang;
} elseif (isset($_SESSION['lang'])) {
    $lang = $_SESSION['lang'];
} else {
    $lang = 'nl';
}

// Handle reset
if (isset($_GET['reset'])) {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit;
}

// Restore previous answers
$answers = $_SESSION['answers'] ?? [];

// Labels
$labels = [
    1 => ["nl"=>"Sterk mee oneens","en"=>"Strongly disagree"],
    2 => ["nl"=>"Mee oneens","en"=>"Disagree"],
    3 => ["nl"=>"Neutraal","en"=>"Neutral"],
    4 => ["nl"=>"Mee eens","en"=>"Agree"],
    5 => ["nl"=>"Sterk mee eens","en"=>"Strongly agree"]
];

// Form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST as $qid => $value) {
        $_SESSION['answers'][$qid] = $value;
    }
    header("Location: score.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
<meta charset="UTF-8">
<title><?= $lang === 'en' ? "Neutral Voting Guide" : "Neutrale Stemwijzer" ?></title>
<link rel="stylesheet" href="vendor/bulma.min.css">
<style>
@font-face {
    font-family:'JetBrainsMono';
    src:url('assets/JetBrainsMonoNerdFontMono-Regular.ttf') format('truetype');
}

html, body {
    margin:0; padding:0; width:100%; height:100%;
    background:#1a1a1a; color:#fff; font-family:'JetBrainsMono', monospace;
}

.container { max-width:800px; margin:0 auto; padding:1rem; }

.title, .subtitle { text-align:center; margin-bottom:1rem; }
.title_and_subtitle_wrapper {
    color: #326146;
    padding: 3%;
    background-color: #232323;
    max-width: 75%;
    text-align: center;
    display: block;
    margin-left: auto;
    margin-right: auto;
    border-radius: 0.5rem;
}
.title { color: #6fcf97; }
.subtitle { color: #326146; }

.label {
    background-color: #326146;
    padding: 0.75rem 1rem;
    margin-bottom: 1rem;
    text-align: center;
    display: block;
    max-width: 50%;
    margin-left: auto;
    margin-right: auto;
    color: #fff;
    font-weight: bold;
}

/* Radio buttons */
.custom-radio {
    appearance:none; -webkit-appearance:none;
    width:22px; height:22px; border:2px solid #56a879;
    margin-bottom:0.25rem; cursor:pointer;
    outline:none; background:#1a1a1a; transition:0.2s;
    border-radius: 0.5rem;
}

.custom-radio:checked { background:#ffffff; border-color:#56a879; }
.custom-radio:hover { transform:scale(1.1); }

.radio-label-text {
    font-size:0.85em; color:#fff; text-align:center;
    display:block; width:55px; margin:0 auto 0.25rem auto;
}

.options-container { display: flex; justify-content: center; gap: 10vw; margin-bottom: 0.5rem; }

.button { border-radius: 10px; min-height: 50px; }
.button.is-primary { background:#6fcf97; border-color:#6fcf97; color:#1a1a1a; }
.button.is-primary:hover { background:#5bb77d; border-color:#5bb77d; }
.button.is-danger { margin-left:1rem; }

.spacer { display:block; width:100%; height:2px; background-color:#373737; border:none; border-radius:2px; margin:20px 0; position:relative; z-index:1; }

.lang-switch-container { display:flex; justify-content:center; align-items:center; gap:0.75rem; margin:1rem 0; font-size:1.2rem; }
.lang-switch-container span { display:inline-block; }

.switch { position: relative; display:inline-block; width:50px; height:25px; }
.switch input { display:none; }
.slider {
    position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0;
    background: #373737; transition: 0.4s; border-radius: 24px; border: 2px solid #56a879;
}
.slider:before {
    position: absolute; content: ""; height: 21px; width: 21px;
    left: -0.5px; bottom: 0; background: #56a879; transition: 0.4s; border-radius: 50%;
}
input:checked + .slider:before { transform: translateX(26px); }
input:checked + .slider { background:#373737; border-color:#56a879; }

.lang-switch-wrapper {
    display:flex;
    justify-content:center;
    align-items:center;
    gap:0.75rem;
    padding: 0.5rem 1rem;
    background-color: #273c32;   /* only behind flags + switch */
    min-height: 50px;
    border-radius: 10px;
}

/* QUESTION CARD */
.question-card {
    background:#1f2f27;
    border-radius:10px;
    overflow:hidden;
    margin:0 auto 0.4rem auto;
    max-width:50%;
}

/* Question text */
.question-header {
    background:#326146;
    padding:0.6rem 1rem;
    font-weight:bold;
    text-align:center;
    border-top-left-radius:10px;
    border-top-right-radius:10px;
}

/* Tabs row flush with card */
.question-tabs {
    display:flex;
    background:#326146;
    border-top:none;
}

.tab {
    flex:1;
    text-align:center;
    padding:0.4rem;
    cursor:pointer;
    background:#326146;
    border-right:1px solid #264c3a;
    border-radius:0;
    transition:0.2s;
    font-weight:bold;
}

.tab:last-child { border-right:none; }
.tab.active {
    background:#264c3a; /* same as expandable area */
    color:#fff;
}

/* Expandable content */
.question-expand {
    background:#264c3a;
    border-bottom-left-radius:10px;
    border-bottom-right-radius:10px;
    padding:0.5rem 0.75rem;
}

.tab-panel { display:none; padding:0.7rem; font-size:0.9em; color:#fff; }

.stance-table {
    width: 100%;
    border-collapse: separate; /* allows border-radius */
    border-spacing: 0;         /* remove spacing */
    margin-top: 0.5rem;
    text-align: center;
    font-weight: bold;
    font-size: 0.85em;
    border: 1px solid #444;
    border-radius: 10px;       /* rounded corners */
    overflow: hidden;          /* clips cells to border radius */
}

/* Rounded corners for headers */
.stance-table th:first-child { border-top-left-radius: 10px; }
.stance-table th:last-child { border-top-right-radius: 10px; }

/* Rounded corners for bottom row */
.stance-table tbody tr:last-child td:first-child { border-bottom-left-radius: 10px; }
.stance-table tbody tr:last-child td:last-child { border-bottom-right-radius: 10px; }

/* Column colors */
.stance-table th.stance-pro { background: #5cb85c; }
.stance-table th.stance-neutral { background: #888888; }
.stance-table th.stance-against { background: #d9534f; }

/* Alternating row colors */
.stance-table tbody tr:nth-child(even) td.stance-pro { background: #4ca64a; }
.stance-table tbody tr:nth-child(odd) td.stance-pro  { background: #5cb85c; }

.stance-table tbody tr:nth-child(even) td.stance-neutral { background: #777; }
.stance-table tbody tr:nth-child(odd) td.stance-neutral  { background: #888; }

.stance-table tbody tr:nth-child(even) td.stance-against { background: #c15c51; }
.stance-table tbody tr:nth-child(odd) td.stance-against  { background: #d9534f; }

/* Cell borders */
.stance-table th, .stance-table td {
    padding: 0.5rem 0;
    border-right: 1px solid #444;
    border-bottom: 1px solid #444;
}

/* Remove right border for last column */
.stance-table th:last-child, .stance-table td:last-child { border-right: none; }
/* Remove bottom border for last row */
.stance-table tbody tr:last-child td { border-bottom: none; }

.filter-container input {
    padding:0.3rem 0.5rem;
    border-radius:5px;
    border:1px solid #56a879;
    background:#1f2f27;
    color:#fff;
    width:80%;
}
/* FOOTER STYLES */
.footer-container {
    position: fixed;
    bottom: 0;
    left: 0;
    width: 100%;
    background-color: #232323;
    color: #fff;
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 15px;
    border-top-left-radius: 8px;
    border-top-right-radius: 8px;
    transition: transform 0.3s ease;
    transform: translateY(0);
    z-index: 999;
}

.footer-container.collapsed {
    transform: translateY(100%); /* completely hidden */
}

.footer-toggle {
    position: fixed; /* stays attached to left screen edge */
    bottom: 100%;       /* attached to bottom when collapsed */
    left: 93.24%;         /* left side of screen */
    width: 100px;
    height: 30px;
    background: #326146;
    color: #fff;
    text-align: center;
    line-height: 30px;
    font-weight: bold;
    border-top-left-radius: 6px;
    border-top-right-radius: 6px;
    cursor: pointer;
    user-select: none;
    z-index: 1000;
}

.footer-img {
    width: 40px; height: 40px;
    border-radius: 50%;
    border: 2px solid #56a879;
    flex-shrink: 0;
}

.footer-text {
    line-height:1.3em;
    font-size: 0.85em;
}

.footer-text a {
    color: #6fcf97;
    text-decoration: underline;
}
</style>
</head>
<body>
<div class="container">

<!-- Page title & subtitle -->
    <div class="title_and_subtitle_wrapper">
        <h1 class="title" data-nl="Neutrale Stemwijzer" data-en="Neutral Voting Guide"><?= $lang === 'en' ? "Neutral Voting Guide" : "Neutrale Stemwijzer" ?></h1>
        <p class="subtitle" data-nl="De vragen die u hier ziet, zijn zonder vooringenomenheid gekozen door een neutrale partij zonder politieke banden." data-en="The questions you see here were chosen without bias by a neutral party without ties to politics."><?= $lang === 'en' ? "The questions you see here were chosen without bias by a neutral party without ties to politics." : "De vragen die u hier ziet, zijn zonder vooringenomenheid gekozen door een neutrale partij zonder politieke banden." ?></p>
    </div>
<hr class="spacer">

<!-- Language switch -->
<div class="lang-switch-container">
<div class="lang-switch-wrapper">
  <span>🇳🇱</span>
  <label class="switch">
    <input type="checkbox" id="langSwitch" <?= $lang==='en'?'checked':'' ?>>
    <span class="slider"></span>
  </label>
  <span>🇬🇧</span>
</div>
<a href="?reset=1" class="button is-danger" data-nl="Reset antwoorden" data-en="Reset answers"><?= $lang==='en' ? "Reset answers" : "Reset antwoorden" ?></a>
</div>

<hr class="spacer">

<!-- Questions -->
<form method="POST" id="questionForm">
<?php foreach ($data['questions'] as $index => $q):
    $qid = 'q'.$q['id'];
    $value = $answers[$qid] ?? '';
?>
<div class="question-card">

    <!-- Tabs -->
<div class="question-tabs">
    <div class="tab active" onclick="togglePanel(this,'tab-question-<?= $q['id'] ?>')"
         data-nl="❓Vraag" data-en="❓Question">
        <?= $lang==='en' ? '❓Question' : '❓Vraag' ?>
    </div>
    <div class="tab" onclick="togglePanel(this,'tab-stance-<?= $q['id'] ?>')"
         data-nl="🏛 Standpunt" data-en="🏛 Stance">
        <?= $lang==='en' ? '🏛 Stance' : '🏛 Standpunt' ?>
    </div>
    <div class="tab" onclick="togglePanel(this,'tab-info-<?= $q['id'] ?>')"
         data-nl="📘 Uitleg" data-en="📘 Info">
        <?= $lang==='en' ? '📘 Info' : '📘 Uitleg' ?>
    </div>
</div>

    <!-- Expandable content -->
    <div class="question-expand">
        <!-- QUESTION PANEL (default open) -->
        <div class="tab-panel" id="tab-question-<?= $q['id'] ?>" style="display:block;"
     data-nl="<?= htmlspecialchars($q['text_nl']) ?>"
     data-en="<?= htmlspecialchars($q['text_en']) ?>">
    <div style="padding:0.5rem 1rem; color:#fff; font-weight:bold; text-align:center;">
        <?= htmlspecialchars($lang==='en' ? $q['text_en'] : $q['text_nl']) ?>
    </div>
</div>

<!-- STANCE PANEL -->
<div class="tab-panel" id="tab-stance-<?= $q['id'] ?>">

    <?php
    // Sort parties into stance groups
    $columns = ['pro'=>[], 'neutral'=>[], 'against'=>[]];
    foreach ($data['parties'] as $party => $positions) {
        $stance = $positions[$q['id']];
        if ($stance <= 2) $columns['against'][] = $party;
        elseif ($stance == 3) $columns['neutral'][] = $party;
        else $columns['pro'][] = $party;
    }

    // Max rows for proper table
    $max_rows = max(count($columns['pro']), count($columns['neutral']), count($columns['against']));
    ?>

    <table class="stance-table">
    <thead>
        <tr>
            <th class="stance-pro" data-nl="Voor" data-en="Agree"><?= $lang==='en' ? 'Agree' : 'Voor' ?></th>
            <th class="stance-neutral" data-nl="Neutraal" data-en="Neutral"><?= $lang==='en' ? 'Neutral' : 'Neutraal' ?></th>
            <th class="stance-against" data-nl="Tegen" data-en="Against"><?= $lang==='en' ? 'Against' : 'Tegen' ?></th>
        </tr>
    </thead>
        <tbody>
            <?php for ($i=0; $i<$max_rows; $i++): ?>
            <tr>
                <td class="stance-pro"><?= isset($columns['pro'][$i]) ? htmlspecialchars($columns['pro'][$i]) : '' ?></td>
                <td class="stance-neutral"><?= isset($columns['neutral'][$i]) ? htmlspecialchars($columns['neutral'][$i]) : '' ?></td>
                <td class="stance-against"><?= isset($columns['against'][$i]) ? htmlspecialchars($columns['against'][$i]) : '' ?></td>
            </tr>
            <?php endfor; ?>
        </tbody>
    </table>

</div>

        <!-- INFO PANEL -->
        <div class="tab-panel" id="tab-info-<?= $q['id'] ?>"
     data-nl="<?= htmlspecialchars($q['explanation_nl'] ?? 'Nog geen uitleg toegevoegd.') ?>"
     data-en="<?= htmlspecialchars($q['explanation_en'] ?? 'No explanation yet.') ?>">
    <div style="padding:0.5rem 1rem; color:#fff; font-size:0.9em;">
        <?= htmlspecialchars(
            $lang==='en'
            ? ($q['explanation_en'] ?? 'No explanation yet.')
            : ($q['explanation_nl'] ?? 'Nog geen uitleg toegevoegd.')
        ) ?>
    </div>
</div>
    </div>
</div>

<!-- RADIO BUTTONS BELOW CARD -->
<div class="options-container">
    <?php for ($i=1;$i<=5;$i++): ?>
        <label>
            <input type="radio"
                   name="<?= $qid ?>"
                   value="<?= $i ?>"
                   class="custom-radio"
                   <?= $value==$i?'checked':'' ?>
                   required>
            <span class="radio-label-text"
                  data-nl="<?= $labels[$i]['nl'] ?>"
                  data-en="<?= $labels[$i]['en'] ?>">
                <?= $labels[$i][$lang] ?>
            </span>
        </label>
    <?php endfor; ?>
</div>

<?php if ($index < count($data['questions'])-1): ?>
<hr class="spacer" style="background-color:#292929;">
<?php endif; ?>

<?php endforeach; ?>

<!-- Submit button -->
<div class="field" style="text-align:center;">
    <hr class="spacer">
    <button type="submit" class="button is-primary" data-nl="Bereken resultaat" data-en="Calculate result"><?= $lang==='en' ? "Calculate result" : "Bereken resultaat" ?></button>
</div>
</form>
</div>

<!-- FOOTER -->
<div class="footer-container" id="footerContainer">
    <div class="footer-toggle" id="footerToggle"> ▲ ▲ ▲ ▲ </div>
    <img src="assets/image01.jpg" alt="Profile Pic" class="footer-img">
    <div class="footer-text">
        <div>© <a href="https://github.xnewton.eu">xnewton</a> 2026</div>
        <div>made with: <a href="https://bulma.io/">Bulma CSS</a></div>
        <hr class="spacer">
        <p>Issue with the site? <a href="mailto:dev@mail.xnewton.eu">Send me an email</a></p>
    </div>
</div>

<script>
// Grab all elements with translations
const langSwitch = document.getElementById('langSwitch');
const translatable = document.querySelectorAll('[data-nl][data-en]');

function togglePanel(tabEl, panelId){
    const parent = tabEl.closest(".question-card");
    parent.querySelectorAll(".tab-panel").forEach(p=>p.style.display="none");
    parent.querySelectorAll(".tab").forEach(t=>t.classList.remove("active"));
    document.getElementById(panelId).style.display="block";
    tabEl.classList.add("active");
}

function filterParties(inputEl, questionId){
    const val = inputEl.value.toLowerCase();
    document.querySelectorAll(`#stance-bar-${questionId} .stance-segment`).forEach(seg=>{
        const name = seg.getAttribute('data-party').toLowerCase();
        seg.style.display = name.includes(val) ? "flex" : "none";
    });
}

function filterByCluster(selectEl, questionId){
    const val = selectEl.value; // 'all', 'left', 'center', 'right'
    document.querySelectorAll(`#stance-bar-${questionId} .stance-segment`).forEach(seg=>{
        const cluster = seg.getAttribute('data-cluster');
        seg.style.display = (val==='all' || cluster===val) ? 'flex' : 'none';
    });
}

//
langSwitch.addEventListener('change', ()=> {
    const lang = langSwitch.checked ? 'en' : 'nl';

    // Update all elements with data-nl/data-en, including question text, info, stance headers, radio labels, buttons, etc.
    document.querySelectorAll('[data-nl][data-en]').forEach(el=>{
        el.textContent = el.getAttribute('data-' + lang);
    });

    // Update URL without reloading
    window.history.replaceState(null, null, "?lang=" + lang);
});

// Toggle footer visibility
const footer = document.getElementById('footerContainer');
const toggle = document.getElementById('footerToggle');

toggle.addEventListener('click', () => {
    footer.classList.toggle('collapsed');
    toggle.textContent = footer.classList.contains('collapsed') ? ' ▼ ▼ ▼ ▼ ' : ' ▲ ▲ ▲ ▲ ';
});
</script>
</body>
</html>