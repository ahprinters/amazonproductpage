<?php
require_once __DIR__ . '/../classes/Flash.php';

$flash = Flash::get();
?>

<?php if ($flash): ?>

    <?php
        $isSuccess = $flash['type'] === 'success';

        $background = $isSuccess ? '#dcfce7' : '#fee2e2';
        $textColor  = $isSuccess ? '#166534' : '#991b1b';
        $borderColor = $isSuccess ? '#86efac' : '#fca5a5';
    ?>

    <div style="max-width: 80rem; margin: 1rem auto 0; padding-left: 1rem; padding-right: 1rem;">
        <div style="
            background-color: <?= $background ?>;
            color: <?= $textColor ?>;
            border: 1px solid <?= $borderColor ?>;
            border-radius: 8px;
            padding: 14px 16px;
            font-size: 14px;
            font-weight: 500;
        ">
            <?= htmlspecialchars($flash['message']) ?>
        </div>
    </div>

<?php endif; ?>