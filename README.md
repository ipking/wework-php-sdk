# About
wework_php 是为了简化开发者对企业微信API接口的使用而设计的，API调用库系列之php版本    
包括企业API接口、消息回调处理方法、第三方开放接口等    
本库仅做示范用，并不保证完全无bug(已经在官方库的基础上修改了不少的BUG)；  
另外，作者会不定期更新本库，但不保证与官方API接口文档同步，因此一切以[官方文档](https://work.weixin.qq.com/api/doc)为准。

本库参考官方PHP库：   
php : https://github.com/sbzhu/weworkapi_php  

# Requirement
经测试，PHP 5.3.3 ~ 7.2.0 版本均可使用

# Director 

├── src // API 接口  
│   ├── Model // API接口需要使用到的一些数据结构  
│   ├── Core //API接口的关键逻辑 
│   ├── Error //异常错误类 
│   └── Util // 基础类方法 
├── callback_json // 消息回调的一些方法  
├── examples // API接口的测试用例 

