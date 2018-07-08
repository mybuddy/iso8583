<?php
/**
 * 报文配置
 *
 * User: yanfei
 * Date: 17/8/7
 * Time: 15:56
 */

/**
 * 报文结构：
 * 报文大小（4位十六进制，占2个字节）+ TPDU + 报文头 + 应用数据。
 * 其中，报文大小 = (TPDU + 报文头 + 应用数据) 的总大小。
 */

// 1. TPDU
$config['tpdu'] = array(
    'ID' => 'N2', // ID
    '目的地址' => 'N4', // 目的地址
    '源地址' => 'N4', // 源地址
);

// 2. 报文头
$config['header'] = array(
    '应用类别定义' => 'N2', // 应用类别定义
    '软件总版本号' => 'N2', // 软件总版本号
    '终端状态' => 'N1', // 终端状态
    '处理要求' => 'N1', // 处理要求
    '软件分版本号' => 'N6', // 软件分版本号
    '位置信息' => 'AN50', // 位置信息
);

// 3. 应用数据

// 3.1 消息类型，N4

// 3.2 位元表，B64

// 3.3 域数据
$config['packet'] = array(
    2 => array('desc' => '主账号', 'type' => 'N..19'), // 主账号
    3 => array('desc' => '交易处理码', 'type' => 'N6'), // 交易处理码
    4 => array('desc' => '交易金额', 'type' => 'N12'), // 交易金额
    //5 => array('desc' => '', 'type' => 'N12'),
    //6 => array('desc' => '', 'type' => 'N12'),
    //7 => array('desc' => '', 'type' => 'N10'),
    //9 => array('desc' => '', 'type' => 'N8'),
    //10 => array('desc' => '', 'type' => 'N8'),
    11 => array('desc' => '受卡方系统跟踪号(POS终端流水号)', 'type' => 'N6'), // 受卡方系统跟踪号（POS终端流水号）
    12 => array('desc' => '受卡方所在地时间', 'type' => 'N6'), // 受卡方所在地时间
    13 => array('desc' => '受卡方所在地日期', 'type' => 'N4'), // 受卡方所在地日期
    14 => array('desc' => '卡有效期', 'type' => 'N4'), // 卡有效期
    15 => array('desc' => '清算日期', 'type' => 'N4'), // 清算日期
    //16 => array('desc' => '', 'type' => 'N4'),
    //18 => array('desc' => '', 'type' => 'N4'),
    //19 => array('desc' => '', 'type' => 'N3'),
    //20 => array('desc' => '', 'type' => 'AN..40'),
    22 => array('desc' => '服务点输入方式码，卡号、密码输入方式', 'type' => array('type' => 'N3', 'align' => 'left')), // 服务点输入方式码，卡号、密码输入方式
    23 => array('desc' => 'IC卡序列号', 'type' => array('type' => 'N3', 'align' => 'right')), // IC卡序列号
    25 => array('desc' => '服务点条件码', 'type' => 'N2'), // 服务点条件码
    26 => array('desc' => '服务点PIN获取码(密码最大长度)', 'type' => 'N2'), // 服务点PIN获取码（密码最大长度）
    //28 => array('desc' => '', 'type' => 'AN9'),
    32 => array('desc' => '受理方标识码(POS中心)', 'type' => 'N..11'), // 受理方标识码（POS中心）
    //33 => array('desc' => '', 'type' => 'N..11'),
    35 => array('desc' => '2磁道数据', 'type' => 'Z..37'), // 2磁道数据
    36 => array('desc' => '3磁道数据', 'type' => 'Z...104'), // 3磁道数据
    37 => array('desc' => '检索参考号', 'type' => 'AN12'), // 检索参考号
    38 => array('desc' => '授权标识应答码(预授权授权号)', 'type' => 'AN6'), // 授权标识应答码（预授权授权号）
    39 => array('desc' => '应答码', 'type' => 'AN2'), // 应答码
    41 => array('desc' => '受卡机终端标识码(device_id)', 'type' => 'ANS8'), // 受卡机终端标识码（device_id）
    42 => array('desc' => '受卡方标识码(merchant_id)', 'type' => 'ANS15'), // 受卡方标识码（merchant_id）
    43 => array('desc' => '商户名称', 'type' => array('type' => 'ANS40', 'encode' => 'GBK')), // 商户名称
    44 => array('desc' => '附加响应数据(发卡方附加响应数据)', 'type' => 'ANS..25'), // 附加响应数据（发卡方附加响应数据）AN还是ANS？？
    //45 => array('desc' => '', 'type' => 'Z..76'),
    //47 => array('desc' => '', 'type' => 'AN...999'),
    48 => array('desc' => '附加数据(私有，结算总额，交易明细)', 'type' => 'N...322'), // 附加数据（私有。结算总额，交易明细）
    49 => array('desc' => '交易货币代码', 'type' => 'AN3'), // 交易货币代码
    //50 => array('desc' => '', 'type' => 'AN3'),
    //51 => array('desc' => '', 'type' => 'AN3'),
    52 => array('desc' => '个人标识码数据(密码密文)', 'type' => 'B64'), // 个人标识码数据（密码密文）
    53 => array('desc' => '安全控制信息(密码、磁道加密类型)', 'type' => 'N16'), // 安全控制信息（密码、磁道加密类型）
    54 => array('desc' => '附加金额(可用余额/累计授权金额)', 'type' => 'AN...20'), // 附加金额（可用余额/累计授权金额）
    55 => array('desc' => 'IC卡数据域', 'type' => 'B...7992'), // IC卡数据域。格式ANS255？？
    //57 => array('desc' => '', 'type' => 'ANS...100'),
    58 => array('desc' => 'PBOC电子钱包标准的交易信息', 'type' => 'ANS...100'), // PBOC电子钱包标准的交易信息
    59 => array('desc' => 'TUSN银联终端唯一标识', 'type' => 'ANS...600'),
    60 => array('desc' => '自定义域', 'type' => 'N...17'), // 自定义域
    61 => array('desc' => '原始信息域', 'type' => 'N...29'), // 原始信息域
    62 => array('desc' => '自定义域', 'type' => 'B...7992'), // 自定义域。格式ANS512？？
    63 => array('desc' => '自定义域', 'type' => 'ANS...163'), // 自定义域
    64 => array('desc' => '报文鉴别码', 'type' => 'B64'), // 报文鉴别码
);