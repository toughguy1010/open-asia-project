<?php
    global $entity_obj, $bookyourtravel_theme_globals;
    $enable_deposit_payments = $bookyourtravel_theme_globals->enable_deposit_payments();

    if ($enable_deposit_payments) {
        ?>
        <div class="deposits_row price_row">
            <p class="deposit-heading"></p>
            <p class="deposit-info"></p>
        </div>
        <?php
    }
