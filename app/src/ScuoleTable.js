import ScuoleRow from './ScuoleRow'
import {useState} from 'react';
export default function ScuoleTable(props){
    const scuole = props.myArray;
    return(
      <table border = "1">
        {scuole.map(s =>
            <ScuoleRow scuola={s} ></ScuoleRow>
        )}
      </table>
    )
    
}