<?php

namespace Manuel90\DummySettings;


class DummySettings {
    public static function getListAvailableSettings() {
        $listSettings = setting('admin.setting_availables','');

        return explode(',',$listSettings);
    }
}