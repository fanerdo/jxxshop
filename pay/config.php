<?php
$config = array (	
		//应用ID,您的APPID。
		'app_id' => "2016081500253672",

		//商户私钥
		'merchant_private_key' => "MIIEogIBAAKCAQEAxRVNWH8ExQp4ogVrf3sn7r/x9u0JtMe+028cHRS9VvtEyXT4TG2BTaZfFBGRTp591YqlHYZ9zbSzm/61UdiQcuhH7syPXP9E/9bZf0ekrkthqVyJLBNTmZ9EwnzBeI/poilrffiwNmHhdEEzaS/A2s9QwSeqUnBzL00XomGVSiI55NMeZTq2hq54LaTOAEsCx9jfOgPN4qh2lUSz2Q5YGhc3osAoONFmVW87dcCG368Nspc8slHez6KltKgTeP4aUjWXllVslnIt1gx2ee6EQgpNTLSuAYY1pZv1kbhpt3ilB/bNj4qEBBJZmjTpjkvaVwrEy7uhdiwdaBoRi+HRIQIDAQABAoIBAFj+x5CAuwynL9YqEGLeoCelsYPJgiqKUFd1wwI2zVEr4eH+2ejZQhxoVmLfk/gmhbgaBXmRs9liEgENQZ+V5ZSd422oHczm0Keew1RAa9Lw40bxLFgk2gly7D/OyibBBcVeYtXdqOTFymMO1kJXeATWvSab3MOhV8IyU8vVuO5iPnHv58h9BORm1HRsvXHDx3tYBPixbhQQ8xq5nTHJwptHJoIy40mw/MAEyYk0B1SwEqD+16PQTPp/EJrV3IAcGjviiOXva7wGiCmMOC9RNxq/C2O+RnXABNlGHxBP0Jyt94aL9Z4Ke0aPf52LfW0/4p9VuP546zLG3dmv8Lx4DAkCgYEA9uDUq9rOc+H5jOvSzoW83azSwYeyzRKuhqYeeVME1cwWlN1jaLMC0/LvwnkiC4EyPwSTvXI4AwCIk15xZkveFM71bnQ4iHLcW39+AaJ8ySxZyJ62ryd6ql/XPVOe0e++PHIpGgks1tvj1WHHhxkUM+o6y6Ku8WpF135Abxj4x4MCgYEAzF14fV4bzLV0z+TIlmOn3EbZ5HWWTbOYF1BJgXHbLI2M6Z+VIsoo9vaCpHspMfShCogCJGGCZomI8WWbdZs44TunO2qIANiINEtpVMmFtacRH/KTUSbFH+R+s07fyGz/a29zvFS6o7gQ2BXv7ZWlIXUzRuz/zSqXL4nrQqsG/4sCgYB+tuuxwb8Rz7zszeYHxrEn/pq2ZtYpWBtoVT9y+l+S9QvqIK/zf15GN31EjjQhP5Dws4K5TDTfpZh0O5ds8Cfu+kMTDGgv8PSVqhyc7i+qei8jQAN4Z4UYidTKT29HRgyYrefWyzrOlVKdpXBqMT8jXmgLOVcd5nQupUihPWoDzQKBgBPBKUd9FFTaaXs7E0SuR2iclK1nzGqgZKBES4auuc/5sthWa7UcM4+DzqcVnKrOic6ZzRM1NcSxoMIs3zUkFwB2ori0mIb0Z14euATRIhKoHpim+ySKCd4GWVT919Xo61u4P0jCK/Rtc3tfy3x8zaSJTraZbDSp3EllHI+qNpf3AoGAZ6vRIGkObzjuTEmuzNwgszCglae2zYLghDoAIotyGXu5+U3rKyRZOBolTt86Rj7gr7w3Xw/3HsotDEbyavyQxc38PGTA/PWVA5sxCx0febzNcc91GH1KEynthmY/pwhyL4sIdpziRF0bKUJ+7CUG+vDlZBetK3t5HbcntODqm9o=",
		
		//异步通知地址
		'notify_url' => "http://local.jxshop.com/pay/notify_url.php",
		
		//同步跳转
		'return_url' => "http://local.jxshop.com/Order/return_url.html",

		//编码格式
		'charset' => "UTF-8",

		//签名方式
		'sign_type'=>"RSA2",

		//支付宝网关
		'gatewayUrl' => "https://openapi.alipaydev.com/gateway.do",

		//支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
		'alipay_public_key' =>"MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAoacFniwxJlpLNDSF8YSyvFkNJYGTne1w5oibZtTt6eWsFwKHv+Q6T6fEImrEmR6iK3jX2yyTad9hXbCDbAlIfrOh6ToVAHGyA1Q6huperKHYtcpwbQhpTuBkRh/MY1Ywh4nB3xOtW7eKRHTS0d7XsiLKPqEyxhWfaDp1FNZwwPDQ1MMvIV0lqIzx0T5OokhYh5fCRbKW2khlROASehpvzEHFi/t7SZ+MRL2gQsBMmDtanarNSnwA+qAc8d8iySqbsccdU8+KUNyAzaBZ7yDqTwKjSQIwIwnn1SQARJvFXHNJULVDbQkpgXPRdy5qhmUPOgfv3M9K+vKPTFF3vDyufwIDAQAB",
);