<?php
/**
 * Created by PhpStorm.
 * User: vlabutin
 * Date: 03.10.18
 * Time: 11:21
 */

namespace App\Repositories\Contracts;


interface RepresentationTraitContract
{

//	public function representations($id); // метод для реалізації витягу репрезентацій для об'єктів

	public function decode($value);

	public function formMediaArray($value);

	public function formMetadata($value);

	public function decodeRepresentation();

}