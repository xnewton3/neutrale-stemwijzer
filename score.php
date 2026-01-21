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

// Language strings
$L = [
    'nl'=>[
        'title'=>'Resultaten',
        'subtitle'=>'Lager getal = meer overeenstemming. Klik op kolommen om te sorteren.',
        'back'=>'Terug naar vragen'
    ],
    'en'=>[
        'title'=>'Results',
        'subtitle'=>'Lower number = more agreement. Click columns to sort.',
        'back'=>'Back to questions'
    ]
];
$T = $L[$lang];

// Orientation
$orientation = [
    "D66"=>"Center","PVV"=>"Right","VVD"=>"Right","GL-PvdA"=>"Left","CDA"=>"Center",
    "JA21"=>"Right","FvD"=>"Right","BBB"=>"Center","DENK"=>"Left","SGP"=>"Right",
    "ChristenUnie"=>"Center","PvdD"=>"Left","SP"=>"Left","Volt"=>"Center","50PLUS"=>"Center"
];

// Calculate scores
$scores=[];
foreach($data['parties'] as $party=>$positions){
    $score=0;
    foreach($_SESSION['answers'] ?? [] as $qid=>$answer){
        $qid_num=str_replace('q','',$qid);
        if(isset($positions[$qid_num])) $score += abs($positions[$qid_num]-$answer);
    }
    $scores[$party]=$score;
}
asort($scores);
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<?php
session_start();
$data = json_decode(file_get_contents("data.json"), true);

// Handle language switch in PHP session
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

// Language strings
$L = [
    'nl'=>[
        'title'=>'Resultaten',
        'subtitle'=>'Lager getal = meer overeenstemming. Klik op kolommen om te sorteren.',
        'back'=>'Terug naar vragen'
    ],
    'en'=>[
        'title'=>'Results',
        'subtitle'=>'Lower number = more agreement. Click columns to sort.',
        'back'=>'Back to questions'
    ]
];
$T = $L[$lang];

// Orientation
$orientation = [
"D66"=>"Center","PVV"=>"Right","VVD"=>"Right","GL-PvdA"=>"Left","CDA"=>"Center",
"JA21"=>"Right","FvD"=>"Right","BBB"=>"Center","DENK"=>"Left","SGP"=>"Right",
"ChristenUnie"=>"Center","PvdD"=>"Left","SP"=>"Left","Volt"=>"Center","50PLUS"=>"Center"
];

// Calculate scores
$scores=[];
foreach($data['parties'] as $party=>$positions){
    $score=0;
    foreach($_SESSION['answers'] ?? [] as $qid=>$answer){
        $qid_num=str_replace('q','',$qid);
        if(isset($positions[$qid_num])) $score += abs($positions[$qid_num]-$answer);
    }
    $scores[$party]=$score;
}
asort($scores);
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
<meta charset="UTF-8">
<title><?= $T['title'] ?></title>
<link rel="stylesheet" href="vendor/bulma.min.css">
<style>
@font-face { font-family:'JetBrainsMono'; src:url('assets/JetBrainsMonoNerdFontMono-Regular.ttf') format('truetype'); }

html,body { margin:0; padding:0; width:100%; height:100%; background:#1a1a1a; color:#fff; font-family:'JetBrainsMono',monospace; }

.container { max-width:800px; margin:0 auto; padding:1rem; }

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

.title { color:#6fcf97; margin-bottom:0.5rem; }
.subtitle { color:#326146; margin-top:0; }

/* Spacers like index.php */
.spacer { display:block; width:100%; height:2px; background-color:#373737; border:none; border-radius:2px; margin:20px 0; position:relative; z-index:1; }

/* Buttons like index.php */
.button { border-radius: 10px; min-height: 50px; }
.button.is-primary { background:#6fcf97; border-color:#6fcf97; color:#1a1a1a; }
.button.is-primary:hover { background:#5bb77d; border-color:#5bb77d; }
.button.is-danger { margin-left:1rem; }

/* Language switch like index.php */
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
    display:flex; justify-content:center; align-items:center; gap:0.75rem;
    padding:0.5rem 1rem; background-color:#273c32; border-radius:10px; min-height:50px;
}

/* Score table styles (unchanged) */
.scoreboard { width:100%; border-collapse: separate; border-spacing:0; background:#52a073; border-radius:12px; overflow:hidden; margin-top:2rem; }
.scoreboard th, .scoreboard td { padding:0.5rem 1rem; text-align:left; }
.scoreboard th { background:#326146; color:#1a1a1a; cursor:pointer; }
.scoreboard tr:nth-child(even) td { background:#56a879; }
.score-left,.score-center,.score-right { color:#fff; }
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

<div class="title_and_subtitle_wrapper">
    <h1 class="title" data-nl="<?= $L['nl']['title'] ?>" data-en="<?= $L['en']['title'] ?>"><?= $T['title'] ?></h1>
    <p class="subtitle" data-nl="<?= $L['nl']['subtitle'] ?>" data-en="<?= $L['en']['subtitle'] ?>"><?= $T['subtitle'] ?></p>
</div>

<hr class="spacer">

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

<!-- SCORE TABLE -->
<table class="scoreboard" id="scoreboard">
<tr>
<th data-type="string" data-nl="Partij" data-en="Party">Partij</th>
<th data-type="number" data-nl="Score" data-en="Score">Score</th>
<th data-type="string" data-nl="Oriëntatie" data-en="Orientation">Oriëntatie</th>
</tr>
<?php foreach($scores as $party=>$score):
$ori=strtolower($orientation[$party]);
$class=$ori==='left'?'score-left':($ori==='right'?'score-right':'score-center');
?>
<tr>
<td><?= htmlspecialchars($party) ?></td>
<td><?= $score ?></td>
<td class="<?= $class ?>"><?= htmlspecialchars($orientation[$party]) ?></td>
</tr>
<?php endforeach; ?>
</table>

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
// Sorting
const getCellValue = (tr, idx) => tr.children[idx].innerText || tr.children[idx].textContent;
const comparer = (idx, asc, type) => (a,b)=>{let v1=getCellValue(a,idx),v2=getCellValue(b,idx); if(type==='number'){v1=parseFloat(v1);v2=parseFloat(v2);} return (v1>v2?1:v1<v2?-1:0)*(asc?1:-1);};
document.querySelectorAll('.scoreboard th').forEach((th,idx)=>{
th.addEventListener('click',()=>{
const table = th.closest('table');
Array.from(table.querySelectorAll('tr:nth-child(n+2)')).sort(comparer(idx, th.asc = !th.asc, th.dataset.type)).forEach(tr=>table.appendChild(tr));
});
});

// Language switch like index.php (no reload)
const langSwitch = document.getElementById('langSwitch');
const translatable = document.querySelectorAll('[data-nl][data-en]');

langSwitch.addEventListener('change', ()=> {
    const lang = langSwitch.checked ? 'en':'nl';
    translatable.forEach(el => el.textContent = el.getAttribute('data-' + lang));
    // No reload, just update PHP session if you want persistent storage
    // Optionally, send AJAX to update session on server
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