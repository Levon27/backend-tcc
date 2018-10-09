# backend-tcc

Rotas implementadas: 

<b>Usuario:</b>

- /usuario: <b>POST </b>

Cria o usuário

   {
   
    "nome":"jonas silva"
  
    "email":"jonas@maua.br"
  
    "senha":"jonas123456"
  
    "cidade":"São Caetano"
  
   }

- /login: <b>POST </b>

Efetua login. Autenticação é feita por hash md5

   {

      "email":"teste@teste.br",
      "senha":"senha123"
  
    }

- /logout: <b>POST </b>

Desloga. Sem parâmetros

- /email: <b> POST </b>

    
    {    
      "email":"destinatario@sustek.br",
      
      "msg":"msg a ser enviada"
      
   
 - /usuario: <b>GET </b>
 
 retorna todas as informações do usuario que esta logado


- /usuario/sensor: <b>GET </b>

Retorna todos os sensores que o usário possui cadastrados.Nao possui parametros. Necessita estar logado

<b>Sensor: </b>

- /sensor: <b>POST </b>

Cadastra um sensor

{

    "id_sensor": "123456789ab"  /* Se possível, enviar o MAC Address do Node */
    
    "equipamento": "geladeira" 
    
}

