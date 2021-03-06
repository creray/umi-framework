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

    $tableScheme->addColumn('publish_time', IColumnScheme::TYPE_DATE);
    $tableScheme->addColumn('title', IColumnScheme::TYPE_VARCHAR);
    $tableScheme->addColumn('title_en', IColumnScheme::TYPE_VARCHAR);
    $tableScheme->addColumn('title_gb', IColumnScheme::TYPE_VARCHAR);
    $tableScheme->addColumn('title_ua', IColumnScheme::TYPE_VARCHAR);
    $tableScheme->addColumn('owner_id', IColumnScheme::TYPE_RELATION);

    $tableScheme->setPrimaryKey('id');
    $tableScheme->addIndex('blog_guid')
        ->addColumn('guid')
        ->setIsUnique(true);
    $tableScheme->addIndex('blog_parent')
        ->addColumn('pid');
    $tableScheme->addIndex('blog_mpath')
        ->addColumn('mpath', 100)
        ->setIsUnique(true);
    $tableScheme->addIndex('blog_pid_slug')
        ->addColumn('pid')
        ->addColumn('slug')
        ->setIsUnique(true);
    $tableScheme->addIndex('blog_uri')
        ->addColumn('uri', 100)
        ->setIsUnique(true);
    $tableScheme->addIndex('blog_type')
        ->addColumn('type', 100);
    $tableScheme->addIndex('blog_owner')
        ->addColumn('owner_id');

    $tableScheme->addConstraint('FK_blog_parent', 'pid', 'umi_mock_hierarchy', 'id', 'CASCADE', 'CASCADE');
    $tableScheme->addConstraint('FK_blog_owner', 'owner_id', 'umi_mock_users', 'id', 'CASCADE', 'CASCADE');

};
