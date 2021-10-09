import React from "react";
import { SefaEnvironmentContainer } from './SafeEnvironment.style';
import { Container } from "@mui/material";

const SafeEnvironment = () => {
     return ( 
          <SefaEnvironmentContainer>
               <Container>
                    Ambiente Seguro <i className={'twf-lock'} />
               </Container>
          </SefaEnvironmentContainer>
     )
}

export default SafeEnvironment;