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

  public function __construct($values = null) {
    if (!is_null($values)) {
      $this->values = $values;
    }
    $this->addSettingsSubmenu();
  }

  public function addSettingsSubmenu() {
    add_submenu_page('options-general.php', 'Einstellungen > Cubbles Runtime', 'Cubbles', 'manage_options', 'cubx_settings', array(&$this, 'addAdminMenuPage'));
  }

  public function getTagList() {
    return implode(',', $this->values['allowedTags']);
  }

  public function addAdminMenuPage() {
    ?>
      <div class="wrap">
        <h1>Settings &gt; Cubbles Runtime</h1>
        <form method="POST">
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
              <th><label for="remoteStoreUrl">Remote Store Url:</label></th>
              <td><input name="remoteStoreUrl" class="regular-text" type="text" value="<?php echo $this->values['remoteStoreUrl']?>"></td>
            </tr>
            <tr>
              <th><label for="rteWebpackage">CRE Webpackage:</label></th>
              <td><input name="rteWebpackage" class="regular-text" type="text" value="<?php echo $this->values['rteWebpackage']?>"></td>
            </tr>
            <tr>
              <td colspan="2">Configure a list of Cubbles components that can be used in posts by users who don't have the capability <code>unfiltered_html</code>.
                Per default only users of type <i>Administrator</i> and <i>Editor</i> have these permission. In a MultiPage Install only <i>Super Admin</i>s do have this capability.<br><br>
                Edit the allowed components below. This needs to be a comma separated list of components e.g. <code>my-component-1,my-component-2</code> allows you to use
                <code>&lt;my-component-1&gt;</code> and <code>&lt;my-component-2&gt;</code> inside the content of a post or page.
              </td>
            </tr>
            <tr>
              <th><label for="allowedTags">Allowed Cubbles Components:</label></th>
              <td><textarea name="allowedTags" class="regular-text" type="text" rows="10" cols="50"><?php echo $this->getTagList()?></textarea></td>
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
}
?>
