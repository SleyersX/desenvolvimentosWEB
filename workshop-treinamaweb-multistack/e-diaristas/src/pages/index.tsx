import type { NextPage } from 'next'
import SafeEnvironment from 'ui/components/feedback/SafeEvironment/SafeEnvironment';
import PageTitle from 'ui/components/data-display/PageTitle/PageTitle';
import UserInformation from 'ui/components/data-display/UserInformation/UserInformation';

const Home: NextPage = () => {
  return (
    <div>
      <SafeEnvironment />
      <PageTitle 
        title={'Conheça os profissionais'} 
        subtitle={'Preencha seu endereço e veja todos os profissionais da sua localidade'}
      />
      <UserInformation 
        name={'Walter Moura'}
        picture={'https://github.com/SleyersX.png'}
        rating={3}
        description={'São Paulo'} 
      />   
    </div>
  )
}

export default Home
