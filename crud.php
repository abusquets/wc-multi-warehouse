<?php

// Create page and insert action
function wc_multi_warehouse_warehouses_create() {

  ?>
  <link type="text/css" href="<?php echo WP_PLUGIN_URL; ?>/wc_multi_warehouse-warehouses/style-admin.css" rel="stylesheet" />
  <?php
  $res = False;
  $code = '';
  $name = '';
  $email = '';
  $public = '';
  $sort = '';
  if (isset($_POST['insert'])) {
    global $wpdb;
    $table_name = "{$wpdb->prefix}wc_warehouse";

    $code = stripslashes($_POST["code"]);
    $name = stripslashes($_POST["name"]);
    $email = $_POST["email"];
    $public = $_POST["public"];
    $sort = $_POST["sort"];

    if ($res = $wpdb->insert(
      $table_name, //table
      array('code' => $code, 'name' => $name, 'email' => $email, 'public' => $public, 'sort' => $sort), //data
      array('%s', '%s', '%s', '%s', '%s') //data format
    )){
      $message = _('Warehouse created');
    }else{
      $message = _('Error');
    }

  }
  ?>
  <link type="text/css" href="<?php echo WP_PLUGIN_URL; ?>/wc_multi_warehouse-warehouses/style-admin.css" rel="stylesheet" />
  <div class="wrap">
    <h2><?php echo _('Add New Warehouse');?></h2>

  <?php
  if (isset($_POST['insert']) && !($res === False)) {
    ?>
    <div class="updated"><p>
      <?php if (isset($message)): ?><div class="updated"><p><?php echo $message; ?></p><?php endif; ?>
      </p></div>
      <a href="<?php echo admin_url('admin.php?page=wc_multi_warehouse_warehouses_list') ?>">&laquo; <?php echo _('Back to warehouses list')?></a>
      <?php
    }else{
      ?>
      <?php if (isset($message)): ?><div class="updated"><p><?php echo $message; ?></p></div><?php endif; ?>
      <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
        <table class='wp-list-table widefat fixed'>
          <tr>
            <th class="ss-th-width"><?php echo _('Code'); ?></th>
            <td><input type="text" name="code" value="<?php echo $code; ?>" class="ss-field-width" /></td>
          </tr>
          <tr>
            <th class="ss-th-width"><?php echo _('Name'); ?></th>
            <td><input type="text" name="name" value="<?php echo $name; ?>" class="ss-field-width" /></td>
          </tr>
          <tr>
            <th class="ss-th-width"><?php echo _('Email'); ?></th>
            <td><input type="text" name="email" value="<?php echo $name; ?>" class="ss-field-width" /></td>
          </tr>
          <tr>
            <th class="ss-th-width"><?php echo _('public'); ?></th>
            <td><input type="text" name="public" value="<?php echo $name; ?>" class="ss-field-width" /></td>
          </tr>
          <tr>
            <th class="ss-th-width"><?php echo _('Order'); ?></th>
            <td><input type="text" name="sort" value="<?php echo $name; ?>" class="ss-field-width" /></td>
          </tr>
        </table>
        <input type='submit' name="insert" value='<?php echo _('Save');?>' class='button'>
      </form>
    </div>
    <?php
  }
}


//List page
function wc_multi_warehouse_warehouses_list() {
  ?>
  <link type="text/css" href="<?php echo WP_PLUGIN_URL; ?>/wc_multi_warehouse-warehouses/style-admin.css" rel="stylesheet" />
  <div class="wrap">
    <h2><?php echo _('Warehouses'); ?></h2>
    <div class="tablenav top">
      <div class="alignleft actions">
        <a href="<?php echo admin_url('admin.php?page=wc_multi_warehouse_warehouses_create'); ?>"><?php echo _('Add New'); ?></a>
      </div>
      <br class="clear">
    </div>
    <?php
    global $wpdb;
    $table_name = "{$wpdb->prefix}wc_warehouse";

    $rows = $wpdb->get_results("SELECT * FROM $table_name ORDER BY public ASC, sort ASC");
    ?>
    <table class='wp-list-table widefat fixed striped posts'>
      <tr>
        <th class="manage-column ss-list-width"><?php echo _('ID'); ?></th>
        <th class="manage-column ss-list-width"><?php echo _('Code'); ?></th>
        <th class="manage-column ss-list-width"><?php echo _('Name'); ?></th>
        <th class="manage-column ss-list-width"><?php echo _('Email'); ?></th>
        <th class="manage-column ss-list-width"><?php echo _('Public'); ?></th>
        <th class="manage-column ss-list-width"><?php echo _('Order'); ?></th>
        <th>&nbsp;</th>
      </tr>
      <?php foreach ($rows as $row) { ?>
        <tr>
          <td class="manage-column ss-list-width"><?php echo $row->id; ?></td>
          <td class="manage-column ss-list-width"><?php echo stripslashes($row->code); ?></td>
          <td class="manage-column ss-list-width"><?php echo stripslashes($row->name); ?></td>
          <td class="manage-column ss-list-width"><?php echo stripslashes($row->email); ?></td>
          <td class="manage-column ss-list-width"><?php echo $row->public; ?></td>
          <td class="manage-column ss-list-width"><?php echo $row->sort; ?></td>
          <td><a href="<?php echo admin_url('admin.php?page=wc_multi_warehouse_warehouses_update&id=' . $row->id); ?>"><?php echo _('Edit'); ?></a></td>
        </tr>
      <?php } ?>
    </table>
  </div>
  <?php
}

// Edit page, update action and delete action
function wc_multi_warehouse_warehouses_update() {
  global $wpdb;
  $table_name = "{$wpdb->prefix}wc_warehouse";
  $id = $_GET["id"];

  $code = '';
  $name = '';
  $email = '';
  $public = '';
  $sort = '';
  //update
  if (isset($_POST['update'])) {
    $code = stripslashes($_POST["code"]);
    $name = stripslashes($_POST["name"]);
    $email = $_POST["email"];
    $public = $_POST["public"];
    $sort = $_POST["sort"];
    $wpdb->update(
      $table_name, //table
      array('code' => $code, 'name' => $name, 'email' => $email, 'public' => $public, 'sort' => $sort), //data
      array('ID' => $id), //where
      array('%s', '%s', '%s', '%s'), //data format
      array('%s') //where format
    );
  }
  //delete
  else if (isset($_POST['delete'])) {
    $wpdb->query($wpdb->prepare("DELETE FROM $table_name WHERE id = %s", $id));
  } else {//selecting value to update
    $s = $wpdb->get_row($wpdb->prepare("SELECT * from $table_name where id=%s", $id));

    $code =  stripslashes($s->code);
    $name =  stripslashes($s->name);
    $email =  stripslashes($s->email);
    $public = $s->public;
    $sort = $s->sort;

  }
  ?>
  <link type="text/css" href="<?php echo WP_PLUGIN_URL; ?>/wc_multi_warehouse-warehouses/style-admin.css" rel="stylesheet" />
  <div class="wrap">
    <h2><? echo _('Warehouses');?></h2>

    <?php if (isset($_POST['delete'])) { ?>
      <div class="updated"><p><?php echo _('Warehouse deleted');?></p></div>
      <a href="<?php echo admin_url('admin.php?page=wc_multi_warehouse_warehouses_list') ?>">&laquo; <?php echo _('Back to warehouses list')?></a>

    <?php } else if (isset($_POST['update'])) { ?>
      <div class="updated"><p><?php echo _('Warehouse updated');?></p></div>
      <a href="<?php echo admin_url('admin.php?page=wc_multi_warehouse_warehouses_list') ?>">&laquo; <?php echo _('Back to warehouses list')?></a>

    <?php } else { ?>
      <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
        <table class='wp-list-table widefat fixed'>
          <tr><th><?php echo _('Code'); ?></th><td><input type="text" name="code" value="<?php echo $code; ?>"/></td></tr>
          <tr><th><?php echo _('Name'); ?></th><td><input type="text" name="name" value="<?php echo $name; ?>"/></td></tr>
          <tr><th><?php echo _('Email'); ?></th><td><input type="text" name="email" value="<?php echo $email; ?>"/></td></tr>
          <tr><th><?php echo _('Public'); ?></th><td><input type="text" name="public" value="<?php echo $public; ?>"/></td></tr>
          <tr><th><?php echo _('Order'); ?></th><td><input type="text" name="sort" value="<?php echo $sort; ?>"/></td></tr>
        </table>
        <input type="submit" name="update" value="<?php echo _('Save')?>" class="button"̣> &nbsp;&nbsp;
        <input type='submit' name="delete" value="<?php echo _('Delete')?>" class="button" onclick="return confirm('&iquest;<?php echo _('Are you sure to delete this warehouse?');?>')">
      </form>
    <?php } ?>

  </div>
  <?php
}
