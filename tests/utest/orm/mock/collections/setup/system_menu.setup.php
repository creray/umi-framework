<?php

use umi\dbal\driver\IColumnScheme;
use umi\orm\metadata\ICollectionDataSource;

return function (ICollectionDataSource $dataSource) {

    $masterServer = $dataSource->getMasterServer();
    $tableScheme = $masterServer->getDbDriver()
        ->addTable($dataSource->getSourceName());

    $tableScheme->setEngine('InnoDB');

    $tableScheme->addColumn('id', IColumnScheme::TYPE_SERIAL);
    $tableScheme->addColumn('guid', IColumnScheme::TYPE_VARCHAR);
    $tableScheme->addColumn('type', IColumnScheme::TYPE_TEXT);
    $tableScheme->addColumn(
        'version',
        IColumnScheme::TYPE_INT,
        [IColumnScheme::OPTION_UNSIGNED => true, IColumnScheme::OPTION_DEFAULT_VALUE => 1]
    );

    $tableScheme->addColumn('pid', IColumnScheme::TYPE_RELATION);
    $tableScheme->addColumn('mpath', IColumnScheme::TYPE_TEXT);
    $tableScheme->addColumn('uri', IColumnScheme::TYPE_TEXT);
    $tableScheme->addColumn('slug', IColumnScheme::TYPE_VARCHAR);
    $tableScheme->addColumn('level', IColumnScheme::TYPE_INT, [IColumnScheme::OPTION_UNSIGNED => true]);
    $tableScheme->addColumn('order', IColumnScheme::TYPE_INT, [IColumnScheme::OPTION_UNSIGNED => true]);
    $tableScheme->addColumn(
        'child_count',
        IColumnScheme::TYPE_INT,
        [IColumnScheme::OPTION_UNSIGNED => true, IColumnScheme::OPTION_DEFAULT_VALUE => 0]
    );

    $tableScheme->addColumn('title', IColumnScheme::TYPE_VARCHAR);
    $tableScheme->addColumn('title_en', IColumnScheme::TYPE_VARCHAR);

    $tableScheme->addColumn('link', IColumnScheme::TYPE_VARCHAR);

    $tableScheme->setPrimaryKey('id');
    $tableScheme->addIndex('menu_guid')
        ->addColumn('guid')
        ->setIsUnique(true);
    $tableScheme->addIndex('menu_parent')
        ->addColumn('pid');
    $tableScheme->addIndex('menu_mpath')
        ->addColumn('mpath', 100)
        ->setIsUnique(true);
    $tableScheme->addIndex('menu_type')
        ->addColumn('type', 100);

    $tableScheme->addConstraint('FK_menu_parent', 'pid', 'umi_mock_menu', 'id', 'CASCADE', 'CASCADE');

};
