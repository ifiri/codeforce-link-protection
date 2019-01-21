<?php

namespace TeaLinkProtection\App\Links\Aliases;

use TeaLinkProtection\App\Libraries;
use TeaLinkProtection\App\Users;
use TeaLinkProtection\App\Config;
use TeaLinkProtection\App\Contracts;

class Factory {
    private $alias_params = [];
    private $created_aliases = [];

    public function __construct() {
        $AliasParamsRepository = new Params\Repository;

        $this->alias_params = $AliasParamsRepository->get();
    }

    public function create_for($Link) {
        $alias_id = $this->get_alias_id_for($Link);

        $AliasEntity = $this->create_by($alias_id);

        return $AliasEntity;
    }

    public function create_by($alias_id) {
        if($this->is_alias_already_created($alias_id)) {
            return $this->get_alias_from_store($alias_id);
        }

        $AliasEntity = new Alias($alias_id);

        return $AliasEntity;
    }

    private function get_actual_alias_data_for($Link) {
        // todo depincly
        $ConfigRepository = new Config\Repository;
        $AliasesRepository = new Repository($ConfigRepository);
        $AliasParamsRepository = new Params\Repository;

        $link_rules = $Link->get_rules();

        // Получаем алиасы, если они есть, для актуальных параметров
        $actual_params = $AliasParamsRepository->get_actual_params_by($link_rules);

        $aliases = $AliasesRepository->get_by($Link->get_ID(), $actual_params, 1);
        $actual_alias_data = end($aliases);

        return $actual_alias_data;
    }

    private function get_alias_id_for($Link) {
        // todo depincly
        $ConfigRepository = new Config\Repository;
        $AliasesRepository = new Repository($ConfigRepository);
        // todo морфер делает 1 запрос к базе на 1 правило морфа, сейчас,
        // с получением алиаса перед проверкой, это избыточно
        $AliasMorpher = new Morpher($Link);

        $actual_alias_data = $this->get_actual_alias_data_for($Link);

        $AliasEntity = null;
        if($actual_alias_data && $actual_alias_data['id']) {
            $AliasEntity = $this->create_by($actual_alias_data['id']);

            $this->store_created_alias($AliasEntity);
        }

        $alias_id = null;
        if($AliasMorpher->is_morph_required($AliasEntity)) {
            $address = $AliasMorpher->get_morphed_alias_for($Link->get_ID());

            // todo сохранять в алиасах только те параметры текущего юзера, которые потребуются в рамках правил ссылки данного алиаса
            $alias_id = $AliasesRepository->save_by($Link->get_ID(), $address, $this->alias_params);
        } elseif($actual_alias_data) {
            $alias_id = $actual_alias_data['id'];
        }

        return $alias_id;
    }

    private function store_created_alias($Alias) {
        $this->created_aliases[$Alias->get_id()] = $Alias;
    }

    private function is_alias_already_created($alias_id) {
        return isset($this->created_aliases[$alias_id]);
    }

    private function get_alias_from_store($alias_id) {
        return $this->created_aliases[$alias_id];
    }
}