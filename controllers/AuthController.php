<?php

namespace app\controllers;

use app\models\ProductsComments;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use Yii;

class AuthController extends Controller
{

    public $enableCsrfValidation = false;

    private function generateJwt(\app\models\User $user) {
        $jwt = Yii::$app->jwt;
        $signer = $jwt->getSigner('HS256');
        $key = $jwt->getKey();
        $time = time();

        $jwtParams = Yii::$app->params['jwt'];

        return $jwt->getBuilder()
            ->issuedBy($jwtParams['issuer'])
            ->permittedFor($jwtParams['audience'])
            ->identifiedBy($jwtParams['id'], true)
            ->issuedAt($time)
            ->expiresAt($time + $jwtParams['expire'])
            ->withClaim('uid', $user->id)
            ->getToken($signer, $key);
    }

    /**
     * @throws yii\base\Exception
     */
    private function generateRefreshToken(\app\models\User $user, \app\models\User $impersonator = null): \app\models\UserRefreshTokens {
        $refreshToken = Yii::$app->security->generateRandomString(200);

        // TODO: Don't always regenerate - you could reuse existing one if user already has one with same IP and user agent
        $userRefreshToken = new \app\models\UserRefreshTokens([
            'urf_userID' => $user->id,
            'urf_token' => $refreshToken,
            'urf_ip' => Yii::$app->request->userIP,
            'urf_user_agent' => Yii::$app->request->userAgent,
            'urf_created' => gmdate('Y-m-d H:i:s'),
        ]);
        if (!$userRefreshToken->save()) {
            throw new \yii\web\ServerErrorHttpException('Failed to save the refresh token: '. implode(" | ", $userRefreshToken->getErrorSummary(true)));
        }

        // Send the refresh-token to the user in a HttpOnly cookie that Javascript can never read and that's limited by path
        /*Yii::$app->response->cookies->add(new \yii\web\Cookie([
            'name' => 'refresh-token',
            'value' => $refreshToken,
            'httpOnly' => true,
            'sameSite' => 'none',
            'secure' => true,
            'path' => '/api/refresh-token',  //endpoint URI for renewing the JWT token using this refresh-token, or deleting refresh-token
        ]));*/

        return $userRefreshToken;
    }

    public function actionLogin() {

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $model = new \app\models\LoginForm();
        if ($model->load(Yii::$app->request->getBodyParams()) && $model->login()) {
            $user = Yii::$app->user->identity;
            $token = $this->generateJwt($user);
            $refresh_token = $this->generateRefreshToken($user);

            return [
                'status' => 'ok',
                'access_token' => (string) $token,
                'refresh_token' => (string) $refresh_token->urf_token,
            ];
        } else {
            return $model->getFirstErrors();
        }

    }

    public function actionRefreshToken() {

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $params = Yii::$app->getRequest()->getBodyParams();
        if (!$params["refresh_token"])
            return new \yii\web\UnauthorizedHttpException('No refresh token found.');
        //$refreshToken = Yii::$app->request->cookies->getValue('refresh-token', false);
        $refreshToken = $params["refresh_token"];
        if (!$refreshToken) {
            return new \yii\web\UnauthorizedHttpException('No refresh token found.');
        }

        $userRefreshToken = \app\models\UserRefreshTokens::findOne(['urf_token' => $refreshToken]);

        if (Yii::$app->request->getMethod() == 'POST') {
            // Getting new JWT after it has expired
            if (!$userRefreshToken) {
                return new \yii\web\UnauthorizedHttpException('The refresh token no longer exists.');
            }

            $user = \app\models\User::find()
                ->where(['id' => $userRefreshToken->urf_userID])
                ->andWhere(['not', ['status' => \app\models\User::STATUS_DELETED]])
                ->one();

            if (!$user) {
                $userRefreshToken->delete();
                return new \yii\web\UnauthorizedHttpException('The user is inactive.');
            }

            $token = $this->generateJwt($user);

            return [
                'status' => 'ok',
                'access_token' => (string) $token,
            ];

        } elseif (Yii::$app->request->getMethod() == 'DELETE') {
            // Logging out
            if ($userRefreshToken && !$userRefreshToken->delete()) {
                return new \yii\web\ServerErrorHttpException('Failed to delete the refresh token.');
            }

            return ['status' => 'ok'];
        } else {
            return new \yii\web\UnauthorizedHttpException('The user is inactive.');
        }
    }

}