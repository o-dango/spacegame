<!-- Camilla Piskonen, 0451801
www-sovellukset -->

<?php
session_start();
session_destroy();
?>
<div class="redirect">
    <p>You logged out!</p>
    <button type="button" class="button cancel" name="frontpage" onclick="location.href='index.php'">Frontpage</button>
</div>
<?php
?>
