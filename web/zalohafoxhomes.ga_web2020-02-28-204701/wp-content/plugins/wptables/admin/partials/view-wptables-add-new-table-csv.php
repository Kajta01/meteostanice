<div class="nav-tab-wrapper">
    <a href="#wpt-tab-file" class="nav-tab nav-tab-active" data-type="file"><?= __("File", 'wptables') ?></a>
    <a href="#wpt-tab-url" class="nav-tab" data-type="url"><?= __("URL", 'wptables') ?></a>
    <a href="#wpt-tab-copy" class="nav-tab" data-type="text"><?= __("Copy/Paste", 'wptables') ?></a>
</div>
<input id="input-type" type="hidden" name="input-type-csv">
<div id="wpt-tab-file" class="wpt-tab-content">
    <div id="wpt-error-file-wrong-type" class="wpt-notice wpt-notice-error" style="display: none;">
        <p>
            <?= __('Wrong file type. Please select CSV file.', 'wptables') ?>
        </p>
    </div>
    <div id="wpt-drop-zone" class="wpt-upload-drop-zone">
        <input id="wpt-file-upload" name="csv-data-file" type="file" accept="text/csv" />
        <h3 class="wpt-title"><?= __("Drop file here to upload", 'wptables') ?></h3>
        <p><?= __("or", 'wptables') ?></p>
        <span class="button button-primary wpt-filedrop-btn">
            <span><?= __("Select file", 'wptables') ?></span>
        </span>
        <br>
        <span id="wpt-filename"></span>
    </div>
</div>
<div id="wpt-tab-url" class="wpt-tab-content wpt-wrap">
    <div class="wpt-form-label"><?= __('File URL', 'wptables') ?>:</div>
    <input id="wpt-input-url" type="text" name="csv-data-url" class="large-text" />
</div>
<div id="wpt-tab-copy" class="wpt-tab-content wpt-wrap">
    <div class="wpt-form-label"><?= __('Copy/paste data', 'wptables') ?>:</div>
    <textarea id="wpt-input-copy" class="large-text wpt-code" rows="8" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" name="csv-data-text"></textarea>
</div>
