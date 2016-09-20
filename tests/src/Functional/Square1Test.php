<?php
/**
 * @file
 *
 * Contains \Drupal\Tests\square1\Functional\Square1Test.
 */
namespace Drupal\Tests\square1\Functional;

use Drupal\Tests\BrowserTestBase;
use Drupal\config\Tests\SchemaCheckTestTrait;
use Drupal\user\Entity\Role;

/**
 * Tests Square1 installation profile expectations.
 *
 * @group square1
 * @preserveGlobalState disabled
 * @runTestsInSeparateProcesses
 */
class Square1Test extends BrowserTestBase {
  use SchemaCheckTestTrait;

  /**
   * Active profile name
   *
   * @var string
   */
  protected $profile = 'square1';

  /**
   * The admin user.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $adminUser;

  /**
   * Tests Square1 installation profile.
   */
  function testSquare1InstallationProfile() {
    /*
     * Test front-page
     */
    $this->drupalGet('');
    $this->assertSession()->linkNotExists('Contact');
    $this->assertSession()->pageTextContains('No front page content has been created yet.');
    $this->assertSession()->statusCodeEquals(200);

    // create admin user
    $this->adminUser = $this->drupalCreateUser([
      'access content',
      'administer nodes',
    ]);
    $role = Role::create([
      'id'    => 'admin_theme',
      'label' => 'Admin theme',
    ]);
    $role->grantPermission('view the administration theme');
    $role->save();
    $this->adminUser->addRole($role->id());
    $this->adminUser->save();

    /*
     * Test all imported configuration for conformance. Ensures all imported
     * default configuration is valid when Square1 profile modules are enabled.
     */
    $names = $this->container->get('config.storage')->listAll();
    /* @var \Drupal\Core\Config\TypedConfigManagerInterface $typed_config */
    $typed_config = $this->container->get('config.typed');
    foreach ($names as $name) {
      $config = $this->config($name);
      $this->assertConfigSchema($typed_config, $name, $config->get());
    }

    /*
     * Test that feedback has correct recipient
     */
    /* @var \Drupal\contact\ContactFormInterface $contact_form */
    $contact_form = ContactForm::load('feedback');
    $recipients   = $contact_form->getRecipients();
    $this->assertEqual(['core@augustash.com'], $recipients);
  }
}
