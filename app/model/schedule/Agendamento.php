<?php
/**
 * Agendamento Model
 * @author  Adianti Clinic SaaS
 */
class Agendamento extends TRecord
{
    const TABLENAME = 'agendamento';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'serial';

    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('system_unit_id');
        parent::addAttribute('paciente_id');
        parent::addAttribute('medico_id');
        parent::addAttribute('data_inicio');
        parent::addAttribute('data_fim');
        parent::addAttribute('status');
        parent::addAttribute('observacoes');
        parent::addAttribute('cor');
        parent::addAttribute('created_at');
    }
}
