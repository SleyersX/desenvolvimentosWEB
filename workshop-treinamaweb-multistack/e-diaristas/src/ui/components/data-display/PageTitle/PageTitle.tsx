import React from "react";
import {PageTitleContainer, PageTitleStyled, PageSubtitleStyled} from './PageTitle.style';

interface PageTitleProps{
    title: string;
    // O pipe separa os varios tipos e o '?' indica que essa propriedade não é obrigatória
    subtitle?: string | JSX.Element;
}

const PageTitle: React.FC<PageTitleProps> = (props) => {
    return (
        <PageTitleContainer>
            <PageTitleStyled>
                {props.title}
            </PageTitleStyled>
            <PageSubtitleStyled>
                {props.subtitle}
            </PageSubtitleStyled>
        </PageTitleContainer>
    );
}

export default PageTitle;