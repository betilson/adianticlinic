<?php
/**
 * Atendimento Model (Prontuário)
 * @author  Adianti Clinic SaaS
 */
class Atendimento extends TRecord
{
    const TABLENAME = 'atendimento';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'serial';

    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('system_unit_id');
        parent::addAttribute('agendamento_id');
        parent::addAttribute('paciente_id');
        parent::addAttribute('medico_id');
        parent::addAttribute('data_atendimento');
        parent::addAttribute('queixa_principal');
        parent::addAttribute('historico_doenca');
        parent::addAttribute('exame_fisico');
        parent::addAttribute('diagnostico');
        parent::addAttribute('prescricao');
        parent::addAttribute('created_at');
    }
}
