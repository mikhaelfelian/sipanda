<?php
/**
 * Word Management View
 * 
 * This view displays the word management interface for sentiment analysis
 * 
 * @author    Mikhael Felian Waskito <mikhaelfelian@gmail.com>
 * @created   2025-04-30
 */
?>
<?= $this->extend(theme_path('main')) ?>

<?= $this->section('content') ?>
<section class="content">
    <div class="container-fluid">
        <!-- Flash Messages -->
        <?php if (session()->has('message')): ?>
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <?= session('message') ?>
            </div>
        <?php endif; ?>

        <?php if (session()->has('error')): ?>
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <?= session('error') ?>
            </div>
        <?php endif; ?>

        <!-- Add New Word Card -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Add New Word</h3>
            </div>
            <div class="card-body">
                <!-- Using GET method form -->
                <form action="<?= base_url('words/add') ?>" method="get">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="word">Word</label>
                                <input type="text" class="form-control" id="word" name="word" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="status_word">Status</label>
                                <select class="form-control" id="status_word" name="status_word" required>
                                    <option value="1">Positive</option>
                                    <option value="2">Negative</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="language">Language</label>
                                <input type="text" class="form-control" id="language" name="language" value="en">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="weight">Weight</label>
                                <input type="number" step="0.01" class="form-control" id="weight" name="weight" value="1.00">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="category">Category</label>
                                <input type="text" class="form-control" id="category" name="category">
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Word</button>
                </form>
            </div>
        </div>

        <!-- Word List Tabs -->
        <div class="card">
            <div class="card-header p-0">
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link active" href="#positive" data-toggle="tab">Positive Words</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#negative" data-toggle="tab">Negative Words</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <!-- Positive Words Tab -->
                    <div class="tab-pane active" id="positive">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 50px">ID</th>
                                        <th>Word</th>
                                        <th>Language</th>
                                        <th>Weight</th>
                                        <th>Category</th>
                                        <th style="width: 150px">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($positiveWords as $word): ?>
                                        <tr>
                                            <td><?= $word['id'] ?></td>
                                            <td><?= esc($word['word']) ?></td>
                                            <td><?= esc($word['language']) ?></td>
                                            <td><?= $word['weight'] ?></td>
                                            <td><?= esc($word['category'] ?? '-') ?></td>
                                            <td>
                                                <a href="javascript:void(0)" class="btn btn-sm btn-info edit-word" 
                                                   data-id="<?= $word['id'] ?>"
                                                   data-word="<?= esc($word['word']) ?>"
                                                   data-status="<?= $word['status_word'] ?>"
                                                   data-language="<?= esc($word['language']) ?>"
                                                   data-weight="<?= $word['weight'] ?>"
                                                   data-category="<?= esc($word['category'] ?? '') ?>">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="<?= base_url('words/delete/' . $word['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this word?');">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($positiveWords)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center">No positive words found</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Negative Words Tab -->
                    <div class="tab-pane" id="negative">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 50px">ID</th>
                                        <th>Word</th>
                                        <th>Language</th>
                                        <th>Weight</th>
                                        <th>Category</th>
                                        <th style="width: 150px">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($negativeWords as $word): ?>
                                        <tr>
                                            <td><?= $word['id'] ?></td>
                                            <td><?= esc($word['word']) ?></td>
                                            <td><?= esc($word['language']) ?></td>
                                            <td><?= $word['weight'] ?></td>
                                            <td><?= esc($word['category'] ?? '-') ?></td>
                                            <td>
                                                <a href="javascript:void(0)" class="btn btn-sm btn-info edit-word" 
                                                   data-id="<?= $word['id'] ?>"
                                                   data-word="<?= esc($word['word']) ?>"
                                                   data-status="<?= $word['status_word'] ?>"
                                                   data-language="<?= esc($word['language']) ?>"
                                                   data-weight="<?= $word['weight'] ?>"
                                                   data-category="<?= esc($word['category'] ?? '') ?>">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="<?= base_url('words/delete/' . $word['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this word?');">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($negativeWords)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center">No negative words found</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Edit Word Modal -->
<div class="modal fade" id="editWordModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Word</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Using GET method form for editing -->
                <form id="editWordForm" action="" method="get">
                    <div class="form-group">
                        <label for="edit_word">Word</label>
                        <input type="text" class="form-control" id="edit_word" name="word" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_status_word">Status</label>
                        <select class="form-control" id="edit_status_word" name="status_word" required>
                            <option value="1">Positive</option>
                            <option value="2">Negative</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_language">Language</label>
                        <input type="text" class="form-control" id="edit_language" name="language">
                    </div>
                    <div class="form-group">
                        <label for="edit_weight">Weight</label>
                        <input type="number" step="0.01" class="form-control" id="edit_weight" name="weight">
                    </div>
                    <div class="form-group">
                        <label for="edit_category">Category</label>
                        <input type="text" class="form-control" id="edit_category" name="category">
                    </div>
                </form>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveWordEdit">Save changes</button>
            </div>
        </div>
    </div>
</div>

<script>
$(function() {
    // Edit word modal
    $('.edit-word').click(function() {
        var id = $(this).data('id');
        var word = $(this).data('word');
        var status = $(this).data('status');
        var language = $(this).data('language');
        var weight = $(this).data('weight');
        var category = $(this).data('category');
        
        $('#edit_word').val(word);
        $('#edit_status_word').val(status);
        $('#edit_language').val(language);
        $('#edit_weight').val(weight);
        $('#edit_category').val(category);
        
        // Set form action with the ID parameter
        $('#editWordForm').attr('action', '<?= base_url('words/edit/') ?>' + id);
        
        $('#editWordModal').modal('show');
    });
    
    // Save edit button
    $('#saveWordEdit').click(function() {
        $('#editWordForm').submit();
    });
});
</script>
<?= $this->endSection() ?> 