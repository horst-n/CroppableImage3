<?php namespace ProcessWire; ?>

<div id='results'>
    <img src='<?php echo $targetUrl ?>' alt='Cropped image' />
    <ul>
        <li>
            <button class='ui-button ui-widget ui-corner-all ui-state-default' onclick='parent.caiCloseReviewWindow("<?php echo $targetUrl; ?>");'>
                <?php echo $confirmCropText; ?>
            </button>
        </li>
        <?php if ($suffix): ?>
        <li>
            <a class='ui-button ui-widget ui-corner-all ui-state-default ui-priority-secondary' href='<?php echo $backToCropUrl ?>'><?php echo $cropAgainText; ?></a>
        </li>
        <?php endif ?>
    </ul>
    <table>
        <tr><td class='key'>imageUrl</td><td class='value'><?php echo $imageUrl;?></td></tr>
        <tr><td class='key'>suffix</td><td class='value'><?php echo $suffix;?></td></tr>
        <tr><td class='key'>width x height</td><td class='value'><?php echo "{$width} x {$height}";?></td></tr>
        <tr><td class='key'>quality</td><td class='value'><?php echo $quality;?></td></tr>
        <tr><td class='key'>sharpening</td><td class='value'><?php echo $sharpening;?></td></tr>
    </table>
</div>

