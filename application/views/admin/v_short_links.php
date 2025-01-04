<div class="panel panel-primary">
  <div class="panel-heading">
    <h3 class="panel-title">
        Short Links
        <a class="btn-sm btn-warning pull-left" onclick="newShortlink();">Add New Short Link</a>
        <div class="clearfix"></div>
    </h3>
  </div>
</div>

<table id="shortlinks-table" class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>Short Code</th>
            <th>Original URL</th>
            <th>Clicks</th>
            <th>Edit</th>
            <th>Delete</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($short_links as $link): ?>
            <tr>
                <td><?= $link->short_code ?></td>
                <td><?= $link->original_url ?></td>
                <td><?= $link->click_count ?></td>
                <td><i class="fa fa-edit text-success cu" onclick="editShortlink(this, <?= $link->id ?>)"></i></td>
                <td><i class="fa fa-trash text-danger cu" onclick="deleteShortlink(<?= $link->id ?>)"></i></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- Hidden modal for editing/adding short links -->
<div class="hidden">
    <div class="shortlink-modal">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <form class="clearfix">
                    <div class="form-group">
                        <label>Original URL</label>
                        <input type="url" name="original_url" class="form-control update-el original_url">
                    </div>
                    <hr/>
                    <div class="ajax-result" style="margin-bottom: 20px;"></div>
                    <div class="form-group">
                        <button type="button" class="btn btn-primary btn-block btn-lg save-shortlink">
                            <i class="fa fa-check-circle"></i> Save
                        </button>
                    </div>
                    <input type="hidden" name="id" class="form-control update-el id" value="0">
                </form>
            </div>  
        </div>
    </div>
</div>

<script>
    function newShortlink() {
        const $modal = $('<div/>', {'id': 'shortlink-modal'});
        $modal.append('<div class="text-center"><i class="l c-c blue h3"></i></div>');
        popupScreen($modal);

        const $view = $('.shortlink-modal').clone(true);
        $view.find('.save-shortlink').on('click', function () {
            saveShortlink(this);
        });
        $modal.html($view);
    }

    function editShortlink(that, id) {
        const $tr = $(that).closest('tr');
        const originalUrl = $($tr.find('td')[1]).text();

        const $modal = $('<div/>', {'id': 'shortlink-modal'});
        $modal.append('<div class="text-center"><i class="l c-c blue h3"></i></div>');
        popupScreen($modal);

        const $view = $('.shortlink-modal').clone(true);
        $view.find('.original_url').val(originalUrl);
        $view.find('.id').val(id);
        $view.find('.save-shortlink').on('click', function () {
            saveShortlink(this);
        });
        $modal.html($view);
    }

    function saveShortlink(btn) {
        $(btn).addClass('l w h6');
        const form = $(btn).closest('form');
        const data = $(form).serialize();

        $.ajax({
            type: "POST",
            url: 'admin/api/SaveShortlink',
            data: data,
            dataType: "json",
            success: function (response) {
                $(btn).removeClass('l w');
                $(form).find('.ajax-result').html(get_alert(response));
                notify(response.msg, response.status);
                if (response.status === 0) {
                    location.reload();
                }
            },
            error: function () {
                $(btn).removeClass('l w');
                notify('Connection error', 2);
            }
        });
    }

    function deleteShortlink(id) {
        if (!confirm('Are you sure you want to delete this short link?')) return;

        $.ajax({
            type: "POST",
            url: 'admin/api/DeleteShortlink',
            data: {id},
            dataType: "json",
            success: function (response) {
                notify(response.msg, response.status);
                if (response.status === 0) {
                    location.reload();
                }
            },
            error: function () {
                notify('Connection error', 2);
            }
        });
    }
</script>
