<?php

class CRM_Populate_Page_Bicpopulate extends CRM_Core_Page {

  public function run() {

// Get list of SEPA Mandates without BIC filled
    $emptybic = civicrm_api3('SepaMandate', 'get', array(
      'sequential' => 1,
      'return' => "id",
      'bic' => array('IS NULL' => 1),
     'options' => array('limit' => 9999),
    ));

// Send variables to Smarty
    $this->assign('countemptybic', count($emptybic["values"]) );


// now populate the BIC field, using the API from the "littleBIC"-Extension
foreach ($emptybic["values"] as $emptybics) {
    $nextiban = civicrm_api3('SepaMandate', 'getsingle', array(
      'sequential' => 1,
      'return' => "id,iban",
      'id' => $emptybics["id"],
    ));
    // Sends variables to Smarty
    $this->assign('currentID', $nextiban['id'] );
    $this->assign('currentIBAN', $nextiban['iban'] );


    // Find corresponding BIC
    $newbic = civicrm_api3('Bic', 'findbyiban', array(
      'sequential' => 1,
      'iban' => $nextiban['iban'],
    ));
    // Sends variables to Smarty
    $this->assign('newbic', $newbic['bic'] );

    // Update Mandate
    $result = civicrm_api3('SepaMandate', 'create', array(
      'sequential' => 1,
      'id' => $nextiban['id'],
      'bic' => $newbic['bic'],
    ));
};










    parent::run();
  }

}
