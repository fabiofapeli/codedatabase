<?php

namespace CodePress\CodeDatabase\Contracts;


interface CriteriaCollection{
    
    public function addCriteria(CriteriaInterface $criteria); ///Método para adicionar critérios
    
    public function getCriteriaCollection(); // Pega todas as coleções de critérios
    
    public function getByCriteria(CriteriaInterface $criteriaInterface); //Passa um critério e retorno o resultado
    
    public function applyCriteria(); //Será incluído aos métodos do repositório para aplicação de critérios
    
    public function ignoreCriteria($isIgnore = true); // true ignore, false não ignora
    
    public function clearCriteria(); // limpa todos os critério
}

