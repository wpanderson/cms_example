<!-- File: src/Template/Articles/index.ctp -->

<h1>Articles</h1>
<?= $this->Html->link('Add Article', ['action' => 'add']) ?>
<table>
    <tr>
        <th>Title</th>
        <th>Created</th>
        <th>Action</th>
    </tr>

    <!-- Here is where we iterate through our $articles query object, printing out article info -->

    <?php foreach ($articles as $article): ?>
    <tr>
        <td>
            <!-- Adds link for view action. Passes article object. -->
            <?= $this->Html->link($article->title, ['action' => 'view', $article->slug]) ?>
        </td>
        <td>
            <?= $article->created->format(DATE_RFC850) ?>
        </td>
        <td>
            <!-- Adds link for Edit action. Passes article object. -->
            <?= $this->Html->link('Edit', ['action' => 'edit', $article->slug]) ?> |
            <!-- Adds delete button to each article. Example of  -->
            <?= $this->Form->postLink(
                'Delete',
                ['action' => 'delete', $article->slug],
                ['confirm' => 'Are you sure?']
            ) ?>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
