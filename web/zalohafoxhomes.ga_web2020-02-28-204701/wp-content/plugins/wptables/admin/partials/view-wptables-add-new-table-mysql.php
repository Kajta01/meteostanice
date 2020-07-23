<input id="input-type" type="hidden" name="input-type-mysql" value="db-table">
<div class="nav-tab-wrapper">
    <a href="#wpt-tab-table" class="nav-tab nav-tab-active" data-type="db-table"><?= __("Table", 'wptables') ?></a>
    <a href="#wpt-tab-query" class="nav-tab" data-type="mysql-query"><?= __("Query", 'wptables') ?></a>
</div>
<div id="wpt-tab-table" class="wpt-tab-content">
    <p style="font-style: italic">
        <?= sprintf(__("If you do not see the desired database in the list, please make sure that the MySQL user <code>%s</code> has read permissions for that database. ", 'wptables'), DB_USER) ?>
    <br><?= sprintf(__("It can be done by using <a href='%s' target='_blank'>PhpMyAdmin</a> or <a href='%s' target='_blank'>GRANT statement</a>.", 'wptables'),
            'https://docs.phpmyadmin.net/en/latest/privileges.html#assigning-privileges-to-user-for-a-specific-database',
            'https://dev.mysql.com/doc/refman/5.7/en/grant.html') ?>
    </p>
    <div class="wpt-form-label"><?= __('Select database', 'wptables') ?>:</div>
    <select id="db-name" name="db-name" style="width: 100%">
        <?php foreach (WPTables::get_dbs() as $key => $value) : ?>
            <option value="<?= $key ?>"><?= $key ?></option>
        <?php endforeach; ?>
    </select>
    <div class="wpt-form-label"><?= __('Select table', 'wptables') ?>:</div>
    <select id="db-table" name="db-table" style="width: 100%"></select>
</div>
<div id="wpt-tab-query" class="wpt-tab-content">
    <div class="wpt-form-label"><?= __('MySQL Query', 'wptables') ?>:</div>
    <textarea name="data-query" class="large-text wpt-code" rows="8" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"></textarea>
    <div>
        <?= __('Available variables', 'wptables') ?>: <code>$user_id</code> - <?= __('current user id', 'wptables') ?><br>
        <?= sprintf(__("Also, you can use <code>%s</code> filter to extend supported variables using PHP.", 'wptables'), 'wptables_mysql_query') ?>
    </div>
</div>
