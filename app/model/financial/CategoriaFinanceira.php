<?php
/**
 * CategoriaFinanceira Model
 * @author  Adianti Clinic SaaS
 */
class CategoriaFinanceira extends TRecord
{
    const TABLENAME = 'categoria_financeira';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'serial';

    public function __construct($id = NULL)
    {
        parent::__construct($id);
        parent::addAttribute('system_unit_id');
        parent::addAttribute('nome');
        parent::addAttribute('tipo');
    }
}
