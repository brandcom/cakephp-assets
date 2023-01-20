<?php
/**
 * @var \App\View\AppView $this
 * @var array $header
 * @var \League\Csv\MapIterator $rows
 */

use Nette\Utils\Strings;

?>
<div class="table-responsive" style="max-height: 65vh; overflow-y: scroll;">
    <table>
        <thead>
        <tr>
            <?php foreach ($header as $item): ?>
                <th><?= h($item) ?></th>
            <?php endforeach; ?>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($rows as $row): ?>
            <tr>
                <?php foreach ($row as $cell): ?>
                    <td title="<?= h($cell) ?>" onclick="this.innerText = '<?= h($cell) ?>';">
                        <?= Strings::truncate((string)h($cell), 45) ?>
                    </td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
