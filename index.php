<?php
require_once __DIR__ . '/classes/task.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action === 'add') {
            $title = trim($_POST['title'] ?? '');
            $priority = $_POST['priority'] ?? 'Normal';
            
            if (!empty($title)) {
                $newTask = new Task(null, $title, $priority);
                $newTask->create();
            }
        } elseif ($action === 'complete') {
            $id = intval($_POST['id'] ?? 0);
            $task = Task::findById($id);
            if ($task) {
                $task->complete();
            }
        } elseif ($action === 'delete') {
            $id = intval($_POST['id'] ?? 0);
            $task = Task::findById($id);
            if ($task) {
                $task->delete();
            }
        }
        
        header("Location: index.php");
        exit;
    }
}

$activeTasks = Task::getTasksByStatus('active');
$completedTasks = Task::getTasksByStatus('completed');
?>
<!DOCTYPE html>
<html class="light" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>TaskFlow - Dashboard To Do List</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
        tailwind.config = {
          darkMode: "class",
          theme: {
            extend: {
              "colors": {
                      "tertiary-fixed": "#6ffbbe",
                      "surface": "#f7f9fb",
                      "on-secondary-fixed": "#111c2d",
                      "primary-fixed": "#d8e2ff",
                      "on-primary-fixed": "#001a42",
                      "tertiary": "#006947",
                      "on-secondary-fixed-variant": "#3c475a",
                      "secondary-container": "#d5e0f8",
                      "on-error-container": "#93000a",
                      "on-primary-fixed-variant": "#004395",
                      "outline": "#727785",
                      "error": "#ba1a1a",
                      "primary-container": "#2170e4",
                      "on-tertiary": "#ffffff",
                      "on-secondary": "#ffffff",
                      "primary-fixed-dim": "#adc6ff",
                      "surface-dim": "#d8dadc",
                      "surface-container-low": "#f2f4f6",
                      "on-primary": "#ffffff",
                      "surface-container-lowest": "#ffffff",
                      "background": "#f7f9fb",
                      "inverse-surface": "#2d3133",
                      "surface-tint": "#005ac2",
                      "tertiary-fixed-dim": "#4edea3",
                      "on-error": "#ffffff",
                      "surface-container": "#eceef0",
                      "tertiary-container": "#00855b",
                      "surface-bright": "#f7f9fb",
                      "on-surface": "#191c1e",
                      "on-tertiary-fixed": "#002113",
                      "inverse-primary": "#adc6ff",
                      "on-surface-variant": "#424754",
                      "inverse-on-surface": "#eff1f3",
                      "on-background": "#191c1e",
                      "on-tertiary-container": "#f5fff6",
                      "primary": "#0058be",
                      "surface-container-high": "#e6e8ea",
                      "on-primary-container": "#fefcff",
                      "outline-variant": "#c2c6d6",
                      "secondary-fixed-dim": "#bcc7de",
                      "secondary": "#545f73",
                      "surface-container-highest": "#e0e3e5",
                      "on-tertiary-fixed-variant": "#005236",
                      "on-secondary-container": "#586377",
                      "secondary-fixed": "#d8e3fb",
                      "error-container": "#ffdad6",
                      "surface-variant": "#e0e3e5"
              },
              "borderRadius": {
                      "DEFAULT": "0.25rem",
                      "lg": "0.5rem",
                      "xl": "0.75rem",
                      "full": "9999px"
              },
              "spacing": {
                      "container-max": "1200px",
                      "gutter": "24px",
                      "xl": "40px",
                      "lg": "24px",
                      "md": "16px",
                      "sm": "8px",
                      "xs": "4px",
                      "unit": "4px"
              },
              "fontFamily": {
                      "label-sm": ["Inter"],
                      "headline-lg-mobile": ["Inter"],
                      "label-bold": ["Inter"],
                      "body-lg": ["Inter"],
                      "headline-md": ["Inter"],
                      "body-md": ["Inter"],
                      "display": ["Inter"],
                      "headline-lg": ["Inter"]
              },
              "fontSize": {
                      "label-sm": ["12px", {"lineHeight": "16px", "fontWeight": "500"}],
                      "headline-lg-mobile": ["20px", {"lineHeight": "28px", "fontWeight": "600"}],
                      "label-bold": ["12px", {"lineHeight": "16px", "letterSpacing": "0.05em", "fontWeight": "600"}],
                      "body-lg": ["16px", {"lineHeight": "24px", "fontWeight": "400"}],
                      "headline-md": ["20px", {"lineHeight": "28px", "fontWeight": "600"}],
                      "body-md": ["14px", {"lineHeight": "20px", "fontWeight": "400"}],
                      "display": ["36px", {"lineHeight": "44px", "letterSpacing": "-0.02em", "fontWeight": "700"}],
                      "headline-lg": ["24px", {"lineHeight": "32px", "letterSpacing": "-0.01em", "fontWeight": "600"}]
              }
            },
          },
        }
      </script>
<style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            display: inline-block;
            line-height: 1;
            text-transform: none;
            letter-spacing: normal;
            word-wrap: normal;
            white-space: nowrap;
            direction: ltr;
        }
      </style>
</head>
<body class="bg-surface text-on-surface font-body-md min-h-screen pb-24 md:pb-0">
<!-- TopAppBar -->
<header class="bg-surface docked full-width top-0 shadow-sm z-50 sticky">
<div class="flex justify-between items-center px-gutter py-md w-full max-w-container-max mx-auto">
<h1 class="font-display text-display text-primary">TaskFlow</h1>
<div class="hidden md:flex gap-md items-center">
<nav class="flex gap-lg mr-lg">
<a class="font-label-bold text-label-bold text-primary border-b-2 border-primary py-1" href="#">Tasks</a>
</nav>
</div>
</div>
</header>
<main class="max-w-container-max mx-auto px-gutter py-xl">
<div class="max-w-3xl mx-auto space-y-xl"><section class="bg-surface-container-lowest p-lg rounded-xl shadow-[0_2px_10px_0_rgba(0,0,0,0.04)]">
<h2 class="font-headline-md text-headline-md text-on-surface mb-md">Tambah Tugas Baru</h2>
<form method="POST" class="flex flex-col sm:flex-row gap-md">
    <input type="hidden" name="action" value="add">
    <div class="flex-grow relative flex gap-2">
        <input name="title" required class="w-full px-md py-3 rounded-lg border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all font-body-md bg-surface" placeholder="Apa yang ingin Anda kerjakan?" type="text"/>
        <select name="priority" class="px-md py-3 rounded-lg border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all font-body-md bg-surface">
            <option value="High Priority">High Priority</option>
            <option value="Normal" selected>Normal</option>
        </select>
    </div>
    <button type="submit" class="bg-primary text-on-primary px-lg py-3 rounded-lg font-label-bold flex items-center justify-center gap-sm hover:opacity-90 active:scale-95 transition-all">
        <span class="material-symbols-outlined" data-icon="add">add</span>
        Tambah Task
    </button>
</form>
</section>
<section>
<div class="flex justify-between items-end mb-md">
<h2 class="font-headline-md text-headline-md text-on-surface">Daftar Tugas Aktif</h2>
<span class="text-on-surface-variant font-label-sm"><?= count($activeTasks) ?> Tugas Tersisa</span>
</div>
<div class="flex flex-col gap-md">

<?php foreach($activeTasks as $task): ?>
<?php 
    $priority = $task->getPriority();
    $priorityClass = "text-secondary bg-secondary-container/50";
    $borderClass = "border-outline-variant";

    if ($priority == 'High Priority') {
        $priorityClass = "text-primary bg-primary-fixed/30";
        $borderClass = "border-primary";
    } elseif ($priority == 'Normal') {
        $priorityClass = "text-on-tertiary-fixed-variant bg-tertiary-fixed/30";
        $borderClass = "border-outline-variant"; 
    }
?>
<div class="group bg-surface-container-lowest p-md rounded-xl shadow-[0_2px_10px_0_rgba(0,0,0,0.04)] hover:shadow-[0_8px_20px_0_rgba(0,0,0,0.08)] transition-all flex items-center justify-between border-l-4 <?= $borderClass ?>">
<div class="flex items-center gap-md">
<form method="POST" class="m-0">
    <input type="hidden" name="action" value="complete">
    <input type="hidden" name="id" value="<?= $task->getId() ?>">
    <button type="submit" class="w-6 h-6 rounded border-2 border-outline hover:border-tertiary-container flex items-center justify-center transition-colors">
        <span class="material-symbols-outlined text-transparent text-sm" data-icon="check">check</span>
    </button>
</form>
<div>
<p class="font-body-lg text-on-surface"><?= htmlspecialchars($task->getTitle()) ?></p>
<span class="font-label-sm px-2 py-0.5 rounded <?= $priorityClass ?>"><?= htmlspecialchars($priority) ?></span>
</div>
</div>
<form method="POST" class="m-0">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="id" value="<?= $task->getId() ?>">
    <button type="submit" class="text-on-surface-variant hover:text-error opacity-0 group-hover:opacity-100 transition-opacity p-2">
    <span class="material-symbols-outlined" data-icon="delete">delete</span>
    </button>
</form>
</div>
<?php endforeach; ?>

<?php if(count($activeTasks) === 0): ?>
    <div class="text-center p-xl text-on-surface-variant italic">
        Tidak ada tugas aktif. Hore!
    </div>
<?php endif; ?>

</div>
</section>
<section>
<h2 class="font-headline-md text-headline-md text-on-surface mb-md mt-xl">Tugas Selesai</h2>
<div class="space-y-sm">

<?php foreach($completedTasks as $task): ?>
<div class="flex items-center justify-between p-md bg-surface-container-low rounded-lg opacity-60 hover:opacity-100 transition-opacity">
<div class="flex items-center gap-md">
<div class="w-6 h-6 rounded bg-[#10B981] flex items-center justify-center">
<span class="material-symbols-outlined text-white text-sm" data-icon="check">check</span>
</div>
<p class="font-body-md text-on-surface line-through"><?= htmlspecialchars($task->getTitle()) ?></p>
</div>
<form method="POST" class="m-0">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="id" value="<?= $task->getId() ?>">
    <button type="submit" class="text-on-surface-variant hover:text-error p-2">
    <span class="material-symbols-outlined" data-icon="delete">delete</span>
    </button>
</form>
</div>
<?php endforeach; ?>

<?php if(count($completedTasks) === 0): ?>
    <div class="text-center p-md text-on-surface-variant italic">
        Belum ada tugas yang diselesaikan.
    </div>
<?php endif; ?>

</div>
</section></div>
</main>
</body></html>
