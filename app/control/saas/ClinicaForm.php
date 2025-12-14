<?php
/**
 * ClinicaForm
 * @author  Adianti Clinic SaaS
 */
class ClinicaForm extends TPage
{
    private $form;

    public function __construct()
    {
        parent::__construct();
        
        $this->form = new BootstrapFormBuilder('form_Clinica');
        $this->form->setFormTitle('Cadastro de Clínica');
        
        $id = new TEntry('id');
        $nome_fantasia = new TEntry('nome_fantasia');
        
        $this->form->addFields([new TLabel('ID:')], [$id]);
        $this->form->addFields([new TLabel('Nome Fantasia:')], [$nome_fantasia]);
        
        $this->form->addAction('Salvar', new TAction([$this, 'onSave']), 'fa:save green');
        $this->form->addAction('Novo', new TAction([$this, 'onClear']), 'fa:eraser red');
        
        parent::add($this->form);
    }
    
    public function onSave()
    {
        // Implementation
    }
    
    public function onClear()
    {
        $this->form->clear();
    }
}
