# 面试测试项目

## 测试题目
Practical Laravel Task: Batch Quantity Adjustment with Predefined Reasons


Goal:
Build a small standalone Laravel API demo for managing batch quantity adjustments using predefined reasons.


Business context:
A warehouse stores product batches. Each batch has a current quantity.


Sometimes the actual counted quantity in the warehouse does not match the quantity saved in the system. In that case, an inventory adjustment is created to correct the batch quantity.


Every adjustment must have a predefined reason, such as:
•	Physical count correction

•	Damaged items

•	Missing items

•	Data entry correction



Free-text notes may be added for extra details, but the main reason must come from predefined reasons.

Example scenario:
A batch currently has quantity 100 in the system.
After physical counting, the warehouse finds quantity 92.
An adjustment is created to correct the batch quantity from 100 to 92.
The system should keep a record of what changed, why it changed, and update the batch quantity.


Required functionality:


1.	Seed basic demo data

The system should include sample warehouses, products, batches, and adjustment reasons.


2. List adjustment reasons
There should be an API endpoint that returns active predefined reasons that can be used for inventory adjustments.


3. Create an inventory adjustment
The system should allow creating an adjustment for a batch.


When an adjustment is created, the system should:
•	validate that the selected batch exists

•	validate that the selected reason exists

•	validate that the selected reason is active

•	make sure the reason is valid for inventory adjustments

•	record the old quantity before the change

•	record the new quantity

•	calculate the quantity difference

•	save the reason and optional note

•	update the batch quantity safely



4. View an inventory adjustment
The system should allow viewing an adjustment record with its related batch, product, warehouse, and reason.


Technical expectations:
•	Use Laravel migrations, models, controllers, requests, resources, and seeders where appropriate.

•	Use proper model relationships.

•	Use database transactions where stock quantity is changed.

•	Keep validation clear and meaningful.

•	Keep the code simple, readable, and not over-engineered.



Expected deliverables:
•	Source code in a public or shared repository

•	Seed data for testing

•	README with setup instructions

•	Brief explanation of the design decisions

## 环境

- PHP 8.3.15
- MySQL 8.0.34
- Nginx 1.19.1
- Laravel v12.12.2 (https://github.com/laravel/laravel/tree/v12.12.2)

## 实现功能
仓库存储产品批次。每个批次都有数量。实际场景中，盘点的数量与系统保存的数量不一致，从而要调整库存，同时要对这次调整库存进行日志记录。(此次不实现用户功能，所以无鉴权，无用户记录)

## 设计方案
1. 需求方案只处理批次更改问题, 不涉及C端用户侧的商品展示, 不涉及商品详情页等信息, 无redis的添加场景, 因此不添加REDIS服务, 据此, 不考虑redis的击穿等场景, 不考虑事务更改下通过redis双删实现原子性.不添加分布式锁确定幂等等涉及redis的问题.
2. 基于需求设计简易的数据表, 不额外添加不必要数据, 主键,索引 要正常添加, 要有软删除, 确保数据不被物理删除, 从而无法恢复.
3. 接口添加必要的请求的字段判断, 返回统一添加code(默认成功是200), 与http code进行区分. 方便前端对接及后续的日志观察. 接口输出要统一格式.
4. 库存更改通过数据事务实现, 确保原子性. 不涉及主从, 不处理主从延迟等问题, 不过度开发.

## 项目部署

```bash
composer install
cp .env.example .env
php artisan key:generate
```

编辑 `.env` 中的数据库配置：

```dotenv
DB_CONNECTION=mysql
DB_HOST=mysql-master-1 #需要改为实际用到的数据库服务器
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=123456
```

> 需先创建数据库：`CREATE DATABASE laravel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;`

执行迁移与种子：

```bash
php artisan migrate:fresh --seed
```

启动服务:
```bash
php artisan serve --host=0.0.0.0 --port=8000
```
## 数据表设计

```tables
-- 仓库表
CREATE TABLE warehouses (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL DEFAULT '' COMMENT '仓库名称',
  location VARCHAR(255) NOT NULL DEFAULT '' COMMENT '地址',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  is_delete TINYINT(1) NOT NULL DEFAULT 0 COMMENT '是否删除, 0:未删除, 1:已删除'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 商品表
CREATE TABLE products (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL DEFAULT '' COMMENT '名称',
  sku VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'sku',
  quantity INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '产品总数',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  is_delete TINYINT(1) NOT NULL DEFAULT 0 COMMENT '是否删除, 0:未删除, 1:已删除',
  UNIQUE KEY products_sku_unique (sku)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 存储批次表
CREATE TABLE batches (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(255) NOT NULL DEFAULT '' COMMENT '批次编号',
  warehouse_id BIGINT UNSIGNED NOT NULL COMMENT '仓库ID',
  product_id BIGINT UNSIGNED NOT NULL COMMENT '产品ID',
  quantity INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '产品数量',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  is_delete TINYINT(1) NOT NULL DEFAULT 0 COMMENT '是否删除, 0:未删除, 1:已删除',
  UNIQUE KEY batches_code_unique (code),
  KEY batches_warehouse_id_index (warehouse_id),
  KEY batches_product_id_index (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 更改原因
CREATE TABLE adjustment_reasons (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL DEFAULT '' COMMENT '更改原因',
  is_active TINYINT(1) NOT NULL DEFAULT 0 COMMENT '是否激活, 0:未激活, 1:已激活',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  is_delete TINYINT(1) NOT NULL DEFAULT 0 COMMENT '是否删除, 0:未删除, 1:已删除',
  KEY adjustment_reasons_name_index (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 更改记录
CREATE TABLE inventory_adjustments (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  batch_id BIGINT UNSIGNED NOT NULL COMMENT '批次ID',
  reason_id BIGINT UNSIGNED NOT NULL COMMENT '原因ID',
  old_quantity INT UNSIGNED NOT NULL COMMENT '旧数量',
  new_quantity INT UNSIGNED NOT NULL COMMENT '新数量',
  quantity_difference INT NOT NULL COMMENT '数量差异',
  note VARCHAR(255) NOT NULL DEFAULT '' COMMENT '备注',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  is_delete TINYINT(1) NOT NULL DEFAULT 0 COMMENT '是否删除, 0:未删除, 1:已删除',
  KEY inventory_adjustments_batch_id_index (batch_id),
  KEY inventory_adjustments_reason_id_index (reason_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

## API 列表

### 1. 列出在用的库存调整原因

```http
GET /api/adjustment-reasons
```

CURL: 
```
curl --location '127.0.0.1:8000/api/adjustment-reasons'
```

返回体:
```json
{
    "data": [
        {
            "id": 1,
            "name": "Physical count correction",
            "is_active": 1
        },
        {
            "id": 3,
            "name": "Damaged items",
            "is_active": 1
        },
        {
            "id": 5,
            "name": "Missing items",
            "is_active": 1
        }
    ],
    "code": 200,
    "message": "success"
}
```

### 2. 调整库存

```http
POST /api/inventory-adjustments
```

CURL: 
```
curl --location '127.0.0.1:8000/api/inventory-adjustments/store' \
--header 'Content-Type: application/json' \
--data '{
    "batch_id":1,
    "reason_id":1,
    "new_quantity": 12,
    "note": "test"
}'
```

请求体：

```
batch_id: 批次ID, 必填
reason_id: 原因ID, 必填
new_quantity: 新库存数, 必填, 不可为负数
note: 备注, 选填
```

返回体:
```json
{
    "data": {
        "id": 11,
        "batch_id": 1,
        "reason_id": 1,
        "old_quantity": 12,
        "new_quantity": 22,
        "quantity_difference": 10,
        "note": "test",
        "created_at": "2026-10-14T01:23:02.000000Z"
    },
    "code": 200,
    "message": "success"
}
```

### 3. 查看调整记录

```http
GET /api/inventory-adjustments/show/{id}
```

CURL: 
```
curl --location '127.0.0.1:8000/api/inventory-adjustments/show/1'
```


返回体：

```json
{
    "data": {
        "id": 1,
        "batch_id": 1,
        "reason_id": 1,
        "old_quantity": 100,
        "new_quantity": 12,
        "quantity_difference": -88,
        "note": "test",
        "created_at": "2026-10-14T01:14:01.000000Z",
        "batch": {
            "id": 1,
            "code": "BATCH-001",
            "quantity": 12,
            "product": {
                "id": 1,
                "name": "鼠标",
                "sku": "SKU-MOUSE-001"
            },
            "warehouse": {
                "id": 1,
                "name": "一号仓库",
                "location": "广州"
            }
        },
        "reason": {
            "id": 1,
            "name": "Physical count correction",
            "is_active": 1
        }
    },
    "code": 200,
    "message": "success"
}
```