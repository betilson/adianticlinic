<?php
/**
 * ClinicaList
 * @author  Adianti Clinic SaaS
 */
class ClinicaList extends TPage
{
    private $form;
    private $datagrid;
    private $pageNavigation;
    private $loaded;

    public function __construct()
    {
        parent::__construct();
        
        // Basic setup for listing using TStandardListTrait
        // Implementation pending full UI definition
        $this->form = new TQuickForm('form_Clinica');
        
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        
        // ... Layout (VBox) ...
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($this->form);
        $vbox->add(TPanelGroup::pack('', $this->datagrid));
        
        parent::add($vbox);
    }
}
