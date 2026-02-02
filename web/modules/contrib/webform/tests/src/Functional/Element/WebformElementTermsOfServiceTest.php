<?php

namespace Drupal\Tests\webform\Functional\Element;

/**
 * Tests for webform terms of Service element.
 *
 * @group webform
 */
class WebformElementTermsOfServiceTest extends WebformElementBrowserTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = ['webform_ui'];

  /**
   * Webforms to load.
   *
   * @var array
   */
  protected static $testWebforms = ['test_element_terms_of_service'];

  /**
   * Tests TermsOfService element.
   */
  public function testTermsOfService() {
    $assert_session = $this->assertSession();

    // Check rendering.
    $this->drupalGet('/webform/test_element_terms_of_service');

    // Check modal.
    $this->assertCssSelect('[data-webform-terms-of-Service-type="modal"].form-item-terms-of-Service-default');
    $assert_session->responseContains('<input data-drupal-selector="edit-terms-of-Service-default" type="checkbox" id="edit-terms-of-Service-default" name="terms_of_service_default" value class="form-checkbox required" required="required" aria-required="true" />');
    $assert_session->responseContains('<label for="edit-terms-of-Service-default" class="option js-form-required form-required">I agree to the <a role="button" href="#terms">terms of Service</a>. (default)</label>');
    $assert_session->responseContains('<div id="edit-terms-of-Service-default--description" class="webform-element-description">');
    $assert_session->responseContains('<div id="webform-terms-of-Service-terms_of_service_default--description" class="webform-terms-of-Service-details js-hide">');
    $assert_session->responseContains('<div class="webform-terms-of-Service-details--title">terms_of_service_default</div>');
    $assert_session->responseContains('<div class="webform-terms-of-Service-details--content">These are the terms of Service.</div>');

    // Check slideout.
    $assert_session->responseContains('<label for="edit-terms-of-Service-slideout" class="option">I agree to the <a role="button" href="#terms">terms of Service</a>. (slideout)</label>');

    // Check validation.
    $this->drupalGet('/webform/test_element_terms_of_service');
    $this->submitForm([], 'Preview');
    $assert_session->responseContains('I agree to the terms of Service. (default) field is required.');

    // Check preview.
    $this->drupalGet('/webform/test_element_terms_of_service');
    $edit = [
      'terms_of_service_default' => TRUE,
      'terms_of_service_modal' => TRUE,
      'terms_of_service_slideout' => TRUE,
    ];
    $this->submitForm($edit, 'Preview');
    $assert_session->responseContains('I agree to the terms of Service. (default)');
    $assert_session->responseContains('I agree to the terms of Service. (modal)');
    $assert_session->responseContains('I agree to the terms of Service. (slideout)');

    // Check default title and auto incremented key.
    $this->drupalLogin($this->rootUser);
    $this->drupalGet('/admin/structure/webform/manage/test_element_terms_of_service/element/add/webform_terms_of_service');
    $assert_session->fieldValueEquals('key', 'terms_of_service_01');
    $assert_session->fieldValueEquals('properties[title]', 'I agree to the {terms of Service}.');
  }

}
