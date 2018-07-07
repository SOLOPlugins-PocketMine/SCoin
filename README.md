# SCoin
서버 내에서 가상화폐를 거래할 수 있는 플러그인입니다.

<br>

## 체인지로그
* **0.1.1 (2017.08.26)** : 첫 릴리즈
* **1.0.0 (2018.06.24)** : PocketMine-MP API 3.0.0 ~ 4.0.0 호환 패치

<br>

## 가상화폐?
원화, 엔화, 미국 달러처럼 일종의 **돈**입니다. 실물 화폐는 지폐나 동전처럼 실물이 있는 반면 가상화폐는 실물이 아닌 컴퓨터 데이터로 이루어져 있습니다.
컴퓨터 데이터는 얼마든지 해킹하여 조작이 가능하기 때문에 데이터에 대해 신뢰성이 떨어지는데요, 이를 해결하기 위해 나온게 바로 **암호화폐**입니다. 암호 화폐는 여러 사람들이 모여 거대한 네트워크를 형성하여 거래를 검증하기 때문에 해킹으로 부터 안전합니다.

<br>

## 암호화폐는 어떻게 사용되나요
최근 암호화폐는 **투자 수단**으로써 많이 각광받고 있습니다. 암호 화폐의 수량은 한정적이지만 수요가 많기 때문에 값이 오르는데요, 대표적으로 비트코인은 2017년초에 1개당 100만원대에 거래되다가 현재에 이르러서는 500만원에 가깝게 거래되고 있습니다. (2017.08 기준)

<br>

## 이걸 서버에서 어떻게 쓰는거에요?
실제 암호화폐 거래소(코인원, 빗썸)에서 가격 데이터를 다운받은 후 이를 기준으로 서버 내에서 가상으로 구매 또는 판매할 수 있게 해줍니다. 서버 머니로 가상화폐를 구매한 후, 시간이 지나 가상화폐의 가격이 오르면 그만큼 수익을 낼 수 있고, 가격이 내리면 그만큼 손해를 입습니다.

<br>

## 주식 플러그인 같은거네요?
네 주식이라 봐도 무방합니다.

<br>

## 사용 가능한 코인의 종류
|코인명|통화명|
|-|-|
|비트코인|BTC|
|이더리움|ETH|
|라이트코인|LTC|
|이더리움 클래식|ETC|
|리플|XRP|
|비트코인 캐시|BCH|

<br>

## 명령어
|명령어|퍼미션|기본값|설명|
|-|-|-|-|
|`/코인 도움말 [페이지]`|scoin.command.allstatus|ALL|코인에 관한 도움말을 볼 수 있습니다.|
|`/코인 확인 [플레이어]`|scoin.command.allstatus|ALL|자신 또는 다른 사람이 소지한 모든 코인을 확인합니다.|
|`/<코인명 또는 통화명>시세`|scoin.command.coinprice|ALL|코인의 현재 시세를 확인합니다.|
|`/<코인명 또는 통화명>구매 <금액>`|scoin.command.coinpurchase|ALL|해당 코인을 금액만큼(서버 돈 기준) 구매합니다.|
|`/<코인명 또는 통화명>구매 <0~100>%`|scoin.command.coinpurchase|ALL|내 돈 기준으로 코인을 구매합니다. 예로 `50%` 입력시 자신이 가진 돈의 절반만큼 코인을 구매합니다.|
|`/<코인명 또는 통화명>구매 <수량><통화명>`|scoin.command.coinpurchase|ALL|수량만큼 코인을 구매합니다.|
|`/<코인명 또는 통화명>판매 <금액>`|scoin.command.coinsell|ALL|금액만큼 코인을 판매합니다.|
|`/<코인명 또는 통화명>판매 <0~100>%`|scoin.command.coinsell|ALL|내가 가진 코인 수량을 기준으로 판매합니다. 예를 들어 비트코인을 120개 소지하고 있을 때 `50%` 입력 시 비트코인 60개가 판매됩니다.|
|`/<코인명 또는 통화명>판매 <수량><통화명>`|scoin.command.coinsell|ALL|수량만큼 코인을 판매합니다.|
|`/<코인명 또는 통화명>확인 [플레이어]`|scoin.command.coinsee|ALL|자신 또는 다른 플레이어의 코인 수량을 확인합니다.|
|`/<코인명 또는 통화명>주기 <플레이어> <수량>`|scoin.command.coingive|해당 플레이어에게 코인을 수량만큼 줍니다.|
|`/<코인명 또는 통화명>뺏기 <플레이어> <수량>`|scoin.command.cointake|해당 플레이어가 가진 코인을 수량만큼 뺏습니다.|
|`/<코인명 또는 통화명>설정 <플레이어> <수량>`|scoin.command.coinset|해당 플레이어가 가진 코인의 수량을 설정합니다.|

<br>

## 명령어 사용 예시
비트코인(BTC)을 100만원만큼 구매하고 싶습니다.
> /btc구매 1000000
<br>

내 전재산의 30%를 써서 이더리움을 구매할려고 합니다.
> /eth구매 30%
<br>

2.8비트코인 캐시를 구매합니다.
> /bch구매 2.8bch
<br>

내가 소지한 이더리움 클래식을 10만원만큼 팔아야겠습니다
> /etc판매 100000
<br>

망했다... 리플 다 팝니다
> /xrp판매 100%
<br>

내가 가진 코인들 가격이 지금 얼마정도 됬을까?
> /코인 확인

<br>

## setting.yml 설정
플러그인을 넣고 서버를 켜면 플러그인 폴더 내에 **setting.yml** 파일이 생성됩니다. 이 파일을 수정하여 작동 방식을 세부적으로 설정할 수 있습니다.

|설정|설명|
|-|-|
|server|코인 시세를 가져올 서버를 설정합니다. coinone, bithumb 중 하나를 선택할 수 있습니다.|
|available-coins|거래 가능한 코인을 설정합니다.|
|price-broadcast-interval|코인의 시세를 알려주는 주기를 설정합니다. 단위는 초 입니다.|
