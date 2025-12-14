<?php
/**
 * PacienteForm
 * @author  Adianti Clinic SaaS
 */
class PacienteForm extends TPage
{
    private $form;

    public function __construct()
    {
        parent::__construct();
        
        $this->form = new BootstrapFormBuilder('form_Paciente');
        $this->form->setFormTitle('Cadastro de Paciente');
        
        $id = new TEntry('id');
        $nome = new TEntry('nome');
        $cpf = new TEntry('cpf');
        
        $this->form->addFields([new TLabel('ID:')], [$id]);
        $this->form->addFields([new TLabel('Nome:')], [$nome]);
        $this->form->addFields([new TLabel('CPF:')], [$cpf]);
        
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
