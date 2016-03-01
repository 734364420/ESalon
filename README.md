#ESalon
# 数据库配置文件:
    * 请统一在/Common/Lf/LfEnterpoint.php中进行数据库配置,后面的方法已弃用
    * （请参考第一条配置方法）/Application/Common/Conf/config.php中进行数据库配置（已弃用）
    * （请参考第一条配置方法）/Application/User/Conf/config.php中进行用户模块数据库配置(DB_USER数据库用户名,DB_PASSWORD数据库密码,DB_DATABASE数据库名称)（已弃用）
# 缓存文件夹
    * 在根目录下建立Runtime文件夹并赋予写权限
# 微信配置
    * 在/Application/Common/Common/function.php中进行微信参数配置
    
# 初始化
    TRUNCATE TABLE `eagerfor_e_user`;
    TRUNCATE TABLE `eagerfor_e_salon`;
    TRUNCATE TABLE `eagerfor_e_participate`;
    TRUNCATE TABLE `eagerfor_e_iteam`;
    TRUNCATE TABLE `eagerfor_e_competition`;
    TRUNCATE TABLE `eagerfor_e_summary`;
    TRUNCATE TABLE `eagerfor_credit`;
    TRUNCATE TABLE `eagerfor_coupons`;
    TRUNCATE TABLE `eagerfor_weixin_log`;