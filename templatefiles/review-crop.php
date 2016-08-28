<?php namespace ProcessWire; ?>

<div id='results'>
    <img src='<?php echo $targetUrl ?>' alt='Cropped image' />
    <ul>
        <li>
            <button class='ui-button ui-widget ui-corner-all ui-state-default' onclick='parent.caiCloseReviewWindow();'>
                <?php echo $confirmCropText; ?>
            </button>
        </li>
        <?php if ($suffix): ?>
            <li>
                <a class='modal' href='<?php echo $backToCropUrl ?>'><?php echo $cropAgainText; ?></a>
            </li>
        <?php endif ?>
    </ul>
</div>

