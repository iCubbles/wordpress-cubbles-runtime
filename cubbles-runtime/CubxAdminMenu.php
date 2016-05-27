<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
* creates the CubxAdminMenu for configuring the Settings
*/
class CubxAdminMenu {

  // holds the initial values for configuration
  private $values = array(
    'remoteStoreUrl' => '',
    'rteWebpackage' => '',
    'allowedTags' => array()
  );

  // holds array of errors if submitted form data is invalid
  private $valid = true;

  public function __construct($values = null) {
    if (isset($_POST['cubxSettingsSubmitted'])) {
      // remove line breaks and whitespaces
      $this->values = $_POST['values'];
      $this->values['allowedTags'] = preg_replace( '/\r|\n|\s/', '', $this->values['allowedTags']);
      $validation = $this->_validateFormInput($this->values);
      if ($validation === true) {
        $this->valid = true;
        CubxRuntime::saveSettings(array(
          'allowedTags' => explode(',', $this->values['allowedTags']),
          'remoteStoreUrl' => $this->values['remoteStoreUrl'],
          'rteWebpackage' => $this->values['rteWebpackage']
        ));
      } elseif (is_array($validation)) {
        $this->valid = $validation;
      }
    } elseif (!is_null($values)) {
      $this->values = $values;
    }
    $this->addSettingsSubmenu();
  }

  public function addSettingsSubmenu() {
    add_submenu_page('options-general.php', 'Cubbles Runtime', 'Cubbles', 'manage_options', 'cubx_settings', array(&$this, 'addAdminMenuPage'));
  }

  public function addAdminMenuPage() {
    if ($this->valid === true) {
      $notificationClass = 'updated';
      $notification = 'Settings saved.';
    } else {
      $notificationClass = 'error';
      $notification = implode('<br>', $this->valid);
    }

    ?>
      <div class="wrap">
        <h1>Settings &gt; Cubbles Runtime</h1>

        <?php if (isset($_POST['cubxSettingsSubmitted'])) { ?>
          <div id="setting-error-settings_updated" class="<?php  echo $notificationClass ?> settings-error notice is-dismissible">
            <p><strong><?php echo $notification ?></strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Cose this message.</span></button>
          </div>
        <?php } ?>

        <form method="POST">
          <input type="hidden" name="cubxSettingsSubmitted" value="true">
          <p>Here you can adjust configuration for Cubbles Runtime.</p>
          <table class="form-table">
            <tbody>
            <tr>
              <th colspan="2">Runtime Configuration</th>
            </tr>
            <tr>
              <td colspan="2">Define which Store you would like to use and which version of CRE (<b>C</b>lient <b>R</b>untime <b>E</b>xtension)</td>
            </tr>
            <tr>
              <th><label for="values[remoteStoreUrl]">Remote Store Url:</label></th>
              <td><input name="values[remoteStoreUrl]" class="regular-text" type="text" value="<?php echo $this->values['remoteStoreUrl'] ?>"></td>
            </tr>
            <tr>
              <th><label for="values[rteWebpackage]">CRE Webpackage:</label></th>
              <td><input name="values[rteWebpackage]" class="regular-text" type="text" value="<?php echo $this->values['rteWebpackage'] ?>"></td>
            </tr>
            <tr>
              <td colspan="2">Configure a list of Cubbles components that can be used in posts by users who don't have the capability <code>unfiltered_html</code>.
                Per default only users of type <i>Administrator</i> and <i>Editor</i> have these permission. In a MultiPage Install only <i>Super Admin</i>s do have this capability.<br><br>
                Edit the allowed components below. This needs to be a comma separated list of components e.g. <code>my-component-1,my-component-2</code> allows you to use
                <code>&lt;my-component-1&gt;</code> and <code>&lt;my-component-2&gt;</code> inside the content of a post or page.
              </td>
            </tr>
            <tr>
              <th><label for="values[allowedTags]">Allowed Cubbles Components:</label></th>
              <td><textarea name="values[allowedTags]" class="regular-text" type="text" rows="10" cols="50"><?php echo $this->values['allowedTags'] ?></textarea></td>
            </tr>
            </tbody>
          </table>
          <p class="submit">
            <input type="submit" name="submit" class="button button-primary" value="Save changes">
          </p>
        </form>
      </div>
    <?php
  }

  private function _validateFormInput($values) {
    $valid = true;
    $errors = array();

    // validate the allowed tags value
    if (isset($values['allowedTags']) && strlen($values['allowedTags']) > 0) {
      // component name needs to have at least one dash (but not at the beginning)
      // and can have lowercase, uppercase chars and digits 0-9
      $pattern = '/^[^\-][a-zA-Z0-9]*[\-][a-zA-Z0-9\-]*$/';
      $allowedTags = explode(',', $values['allowedTags']);
      foreach ($allowedTags as $tag) {
        if (preg_match($pattern, $tag) === 0) {
          $valid = false;
          $errors['allowedTags'] = 'Please provide a valid Cubble component list!';
        }
      }
    }

    // validate remoteStoreUrl is not empty
    if (isset($values['remoteStoreUrl']) && strlen($values['remoteStoreUrl']) === 0) {
      $valid = false;
      $errors['remoteStoreUrl'] = 'Please provide a valid Remote Store Url!';
    }

    // validate rteWebpackage is not empty
    if (isset($values['rteWebpackage']) && strlen($values['rteWebpackage']) === 0) {
      $valid = false;
      $errors['rteWebpackage'] = 'Please provide a valid CRE Webpackage!';
    }

    if ($valid) {
      return $valid;
    } else {
      return $errors;
    }
  }
}
?>
