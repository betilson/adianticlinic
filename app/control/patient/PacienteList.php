<?php
/**
 * PacienteList
 * @author  Adianti Clinic SaaS
 */
class PacienteList extends TPage
{
    private $form;
    private $datagrid;
    private $pageNavigation;
    private $loaded;

    public function __construct()
    {
        parent::__construct();
        
        $this->form = new TQuickForm('form_Paciente');
        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid);
        
        $vbox = new TVBox;
        $vbox->style = 'width: 100%';
        $vbox->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $vbox->add($this->form);
        $vbox->add(TPanelGroup::pack('', $this->datagrid));
        
        parent::add($vbox);
    }
}
