# backend-tcc

Rotas implementadas: 

<b>Usuario:</b>

- /usuario: <b>POST </b>

Cria o usuário

   {
   
    "nome"
  
    "email"
  
    "senha" (plain text)
  
    "cidade"
  
   }

- /login: <b>POST </b>

Efetua login. Autenticação é feita por hash md5

   {

      "email"
  
      "senha"
  
    }

- /logout: <b>POST </b>

Desloga. Sem parâmetros

- /usuario/sensor: <b>GET </b>

Retorna todos os sensores que o usário possui cadastrados. Necessita estar logado


<b>Sensor: </b>

- /sensor: <b>POST </b>

Cadastra um sensor

{

    "id_sensor": Se possível, enviar o MAC Address do Node
    
    "equipamento": 
    
}

