<?php
/**
 * Created by PhpStorm.
 * User: philipprupp
 * Date: 01.05.15
 * Time: 14:43
 */

class Helper {

    static function travelToArray($travel) {
        $tmp = array();
        $tmp["id"] = $travel->getId();
        $tmp["title"] = $travel->getTitle();
        $tmp["date"] = $travel->getDate();
        $tmp["description"] = $travel->getDescription();
        $tmp["length"] = $travel->getLength();
        $tmp["userid"] = $travel->getUserid();
        return $tmp;
    }

    static function entryToArray($entry) {
        $tmp = array();
        $tmp["id"] = $entry->getId();
        $tmp["title"] = $entry->getTitle();
        $tmp["date"] = $entry->getDate();
        $tmp["time"] = $entry->getTime();
        $tmp["location"] = $entry->getLocation();
        $tmp["text"] = $entry->getText();
        $tmp["image"] = $entry->getImage();
        $tmp["userid"] = $entry->getUserid();
        $tmp["travelid"] = $entry->getTravelid();
        return $tmp;
    }

    static function commentToArray($comment) {
        $tmp = array();
        $tmp["id"] = $comment->getId();
        $tmp["date"] = $comment->getDate();
        $tmp["time"] = $comment->getTime();
        $tmp["text"] = $comment->getText();
        $tmp["userid"] = $comment->getUserid();
        $tmp["entrylid"] = $comment->getEntryid();
        return $tmp;
    }

    static function userToArray($user) {
        $tmp = array();
        $tmp["id"] = $user->getId();
        $tmp["firstname"] = $user->getFirstname();
        $tmp["lastname"] = $user->getLastname();
        $tmp["username"] = $user->getUsername();
        $tmp["password"] = $user->getPassword();
        $tmp["roleid"] = $user->getRoleid();
        return $tmp;
    }

    static function roleToArray($role) {
        $tmp = array();
        $tmp["id"] = $role->getId();
        $tmp["name"] = $role->getName();
        return $tmp;
    }
}