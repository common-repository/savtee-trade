<?php

$UpAjax = new UPAjax();

function getPostElementForm()
{
    global $UpAjax;
    
    $UpAjax->getPostElementDataView();
    
    die();
}

function savePostElementForm()
{
    global $UpAjax;
    
    $UpAjax->savePostElementData();
    
    die();
}

function getSecondaryElements()
{
    global $UpAjax;
    
    $UpAjax->getSecondaryElementsView();
    
    die();
}

function getCategoriesForm()
{
    global $UpAjax;
    
    $UpAjax->getCategoriesFormView();

    die();
}

function saveCategoriesForm()
{
    global $UpAjax;
    
    $UpAjax->saveCategoriesData();

    die();
}

function getNoteForm()
{
    global $UpAjax;
    
    $UpAjax->getNoteFormView();

    die();
}

function saveNoteForm()
{
    global $UpAjax;
    
    $UpAjax->saveNoteData();

    die();
}
/* deprecated
function getFavoriteSelection()
{
    global $UpAjax;
    
    $UpAjax->getFavoriteSelectionView();

    die();
}
* 
* NEW multiple favorites
*/

function setFavoriteStatus()
{
    global $UpAjax;
    
    $UpAjax->setFavoriteStatus();
    
    echo $UpAjax->response;

    die();
}

function updateFavoriteContent()
{
    global $UpAjax;
    
    $UpAjax->updateFavoriteContent();
    
    echo $UpAjax->response;
    
    die();
}